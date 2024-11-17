<?php

require_once '../partials/header.php'; // Adjust path if needed
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if it hasn't started
}

guard(); // Ensure user is logged in

$errors = []; // Initialize an empty array for errors

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close_errors'])) {
    // Clear the errors array when the close button is pressed
    $errors = [];
    // Redirect to prevent form resubmission issues
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $subject_data = [
        'subject_code' => isset($_POST['subject_code']) ? $_POST['subject_code'] : '',
        'subject_name' => isset($_POST['subject_name']) ? $_POST['subject_name'] : ''
    ];

    // Validate subject data (simple validation example)
    if (empty($subject_data['subject_code'])) {
        $errors[] = "Subject code is required.";
    }
    if (empty($subject_data['subject_name'])) {
        $errors[] = "Subject name is required.";
    }

    // If there are no errors, store the subject in the session
    if (empty($errors)) {
        if (!isset($_SESSION['subjects'])) {
            $_SESSION['subjects'] = []; // Initialize the subjects array in the session if not set
        }

        // Add the subject to the session
        $_SESSION['subjects'][] = $subject_data;

        // Clear the form fields after successful submission
        $subject_data = []; // Reset form fields after successful submission
    }
}
?>

<div class="container-fluid">
    <div class="row">
            <?php require_once '../partials/side-bar.php'; ?>
      

      

        <!-- Main Content Column -->
        <div class="col-md-9 col-lg-10">
            <!-- Main Content -->
            <div class="pt-5">
                <h3 class="card-title">Add a New Subject</h3>

                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger position-relative" style="width: 100%; margin-bottom: 15px;">
                    <strong>System Errors</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- Exit Button to close the error alert -->
                    <form method="POST" action="" class="position-absolute top-0 end-0">
                        <button type="submit" name="close_errors" class="btn-close btn-sm" aria-label="Close" style="color: red; font-size: 0.8rem; margin-right: 10px; margin-top: 10px;"></button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Subject Form Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="add.php" method="POST">
                        <div class="mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code" value="<?php echo isset($subject_data['subject_code']) ? htmlspecialchars($subject_data['subject_code']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name" value="<?php echo isset($subject_data['subject_name']) ? htmlspecialchars($subject_data['subject_name']) : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>

            <!-- Subject List Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Subject List</h5>

                    <!-- Subject Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: #d3d3d3; filter: brightness(110%);">
                                <!-- Add subject list data rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
require_once '../partials/footer.php';
?>  
