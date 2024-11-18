<?php
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure the user is logged in

$errors = []; // Error messages array
$success_message = ''; // Success message
$student_data = ['student_id' => '', 'first_name' => '', 'last_name' => ''];

// Fetch the student details for editing
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Make sure to use the primary key `id`
    $conn = connectDatabase();

    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?"); // Ensure this matches your DB column
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
    } else {
        $errors[] = "Student not found.";
    }

    $stmt->close();
    $conn->close();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate input
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($errors)) {
        $conn = connectDatabase();

        // Proceed with the update without checking for identical data
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $first_name, $last_name, $student_id);

        if ($stmt->execute()) {
            $success_message = "Student details updated successfully.";
            header("Location: register.php");
            exit();
        } else {
            $errors[] = "Error updating student. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once '../partials/side-bar.php'; ?>
        <!-- Edit Student Content -->
        <div class="col-md-10">
            <div class="card-body">
                <div class="pt-5">
                    <h3 class="card-title">Edit Student</h3><br>
                    <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>System Errors</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Edit Student Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="hidden" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_data['student_id'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_data['first_name']); ?>" placeholder="Enter First Name">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_data['last_name']); ?>" placeholder="Enter Last Name">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Student</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>
