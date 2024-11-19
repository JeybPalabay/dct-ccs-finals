<?php
// Assuming add-subject.php or similar script for adding a subject
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure user authentication

$errors = []; // Error messages array
$success_message = ''; // Success message

// Handle the form submission for adding subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);

    // Validate input
    if (empty($subject_code)) {
        $errors[] = "Subject code is required.";
    }
    if (empty($subject_name)) {
        $errors[] = "Subject name is required.";
    }

    if (empty($errors)) {
        $conn = connectDatabase();

        // Check for duplicate subject code
        $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_code = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Subject code or name already exists.";
        } else {
            // Insert new subject
            $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $subject_code, $subject_name);

            if ($stmt->execute()) {
                // Set success message
                $success_message = "Subject added successfully.";
                // Redirect to the same page to reset the form
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors[] = "Error adding subject. Please try again.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../partials/side-bar.php'; ?>
        <div class="col-md-9 col-lg-10">
            <div class="pt-5">
                <h3 class="card-title">Add a New Subject</h3><br>
                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Display success message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Display errors -->
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

            <!-- Subject Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" maxlength="4" value="<?php echo isset($_POST['subject_code']) && !empty($errors) ? htmlspecialchars($_POST['subject_code']) : ''; ?>" placeholder="Enter Subject Code">
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo isset($_POST['subject_name']) && !empty($errors) ? htmlspecialchars($_POST['subject_name']) : ''; ?>" placeholder="Enter Subject Name">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>

            <!-- Subject List -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Subject List</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = connectDatabase();
                                $result = $conn->query("SELECT * FROM subjects");

                                if ($result->num_rows > 0):
                                    while ($subject = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo htmlspecialchars($subject['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                            <a href="delete.php?id=<?php echo htmlspecialchars($subject['id']); ?>" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No subjects available.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php $conn->close(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>
