<?php
$pageTitle = "Delete Students";
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure user authentication

$success_message = '';

// Fetch student details for confirmation
$connection = connectDatabase();
$student_id = $_GET['id'] ?? null;

$query = "SELECT * FROM students WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $delete_query = "DELETE FROM students WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param('i', $student_id);

    if ($delete_stmt->execute()) {
        $_SESSION['delete_success'] = "Student deleted successfully!";
        header("Location: register.php"); // Redirect after successful deletion
        exit();
    }

    $delete_stmt->close();
}

$stmt->close();
$connection->close();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Include the sidebar -->
        <?php require_once '../partials/side-bar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
            <!-- Student Details Confirmation -->
            <?php if (!empty($student)): ?>
                <div class="card-body">
                    <h3 class="card-title">Delete Student</h3><br>
                    <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <p>Are you sure you want to delete the following student record?</p>
                        <ul>
                            <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                            <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                            <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                        </ul>
                        <form method="post">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='register.php'">Cancel</button>
                            <button type="submit" name="delete_student" class="btn btn-primary">Delete Student Record</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php
require_once '../partials/footer.php';
?>
