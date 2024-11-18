<?php
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure the user is logged in

$errors = []; // Error messages array
$success_message = ''; // Success message

// Initialize student data
$student_data = ['student_id' => '', 'first_name' => '', 'last_name' => ''];

// Fetch the student details for editing from session
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);

    // Assuming `students` is stored in session for demonstration purposes
    if (isset($_SESSION['students'][$student_id])) {
        $student_data = $_SESSION['students'][$student_id];
    } else {
        $errors[] = "Student not found in session data.";
    }
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // No server-side validation; proceed directly with updating session data
    $_SESSION['students'][$student_id]['first_name'] = $first_name;
    $_SESSION['students'][$student_id]['last_name'] = $last_name;

    $success_message = "Student details updated successfully.";
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
