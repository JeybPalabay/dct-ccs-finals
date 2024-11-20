<?php
$pageTitle = "Edit Subjects";
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure the user is logged in

$errors = []; // Error messages array
$subject_data = ['subject_code' => '', 'subject_name' => ''];

// Fetch the subject details for editing
if (isset($_GET['id'])) {
    $subject_id = intval($_GET['id']);
    $conn = connectDatabase();

    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subject_data = $result->fetch_assoc();
    } else {
        $errors[] = "Subject not found.";
    }

    $stmt->close();
    $conn->close();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $subject_name = trim($_POST['subject_name']);

    // Validate input
    if (empty($subject_name)) {
        $errors[] = "Subject name is required.";
    }

    // If no errors, update the subject name in the database
    if (empty($errors)) {
        $conn = connectDatabase();

        // Check for duplicate subject name (excluding the current subject)
        $stmt = $conn->prepare("SELECT * FROM subjects WHERE (subject_name = ? OR subject_code = ?) AND id != ?");
        $stmt->bind_param("ssi", $subject_name, $subject_data['subject_code'], $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Subject code or name already exists.";
        } else {
            // Update the subject name
            $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
            $stmt->bind_param("si", $subject_name, $subject_id);

            if ($stmt->execute()) {
                // Redirect to the list page
                header("Location: add.php");
                exit();
            } else {
                $errors[] = "Error updating subject. Please try again.";
            }
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
        <!-- Edit Subject Content -->
        <div class="col-md-10">
            <div class="card-body">
                <div class="pt-5">
                    <h3 class="card-title">Edit Subject</h3><br>
                    <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
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

                <!-- Edit Subject Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="POST">
                            <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">
                            <div class="mb-3">
                                <label for="subject_code" class="form-label">Subject Code</label>
                                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject_data['subject_code']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="subject_name" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject_data['subject_name']); ?>" placeholder="Enter Subject Name">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Subject</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>
