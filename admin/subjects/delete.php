<?php
ob_start(); // Start output buffering at the very beginning

require_once __DIR__ . '/../../functions.php'; // Load utility functions
require_once __DIR__ . '/../partials/header.php'; // Load header
require_once __DIR__ . '/../partials/side-bar.php'; // Load side bar

guard(); // Ensure user authentication

// Initialize variables
$error_message = '';
$success_message = '';

// Validate subject ID from URL
$subject_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$subject_id) {
    header("Location: add.php"); // Redirect if no valid ID is provided
    exit();
}

// Fetch subject details for confirmation
$connection = connectDatabase();
$query = "SELECT * FROM subjects WHERE id = ?";
$stmt = $connection->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $connection->error);
}

$stmt->bind_param('i', $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();

if (!$subject) {
    $error_message = "Subject not found.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subject'])) {
    // Handle the delete request
    $delete_query = "DELETE FROM subjects WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);

    if (!$delete_stmt) {
        die("Query preparation failed: " . $connection->error);
    }

    $delete_stmt->bind_param('i', $subject_id);

    if ($delete_stmt->execute()) {
        $_SESSION['delete_success'] = "Subject deleted successfully!";
        header("Location: add.php"); // Redirect after successful deletion
        exit();
    } else {
        $error_message = "Failed to delete the subject: " . $connection->error;
    }

    $delete_stmt->close();
}

$stmt->close();
$connection->close();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">

    <!-- Error Message -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Subject Details Confirmation -->
    <?php if (!empty($subject)): ?>
        <div class="card-body">
            <h3 class="card-title">Delete Subject</h3><br>
            <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <p>Are you sure you want to delete the following subject record?</p>
                <ul>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
                <form method="post">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='add.php'">Cancel</button>
                    <button type="submit" name="delete_subject" class="btn btn-primary">Delete Subject Record</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/../partials/footer.php'; // Load footer
ob_end_flush(); // Flush output buffer
?>
