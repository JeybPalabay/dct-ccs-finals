<?php
require_once '../../functions.php';
require_once '../partials/header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure the user is logged in

$errors = []; // Error messages array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Add/Update Form Submission
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $subject_code = trim($_POST['subject_code']);
        $subject_name = trim($_POST['subject_name']);

        // Validate input
        if (empty($subject_code)) {
            $errors[] = "Subject code is required.";
        } elseif (strlen($subject_code) != 4) {
            $errors[] = "Subject code must be exactly 4 characters.";
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
                $errors[] = "Subject code already exists.";
            } else {
                // Insert new subject
                $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
                $stmt->bind_param("ss", $subject_code, $subject_name);

                if (!$stmt->execute()) {
                    $errors[] = "Error adding subject. Please try again.";
                }
            }

            $stmt->close();
            $conn->close();
        }
    }

    // Handle Delete Action
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $subject_id = intval($_POST['subject_id']);

        $conn = connectDatabase();
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $subject_id);

        if (!$stmt->execute()) {
            $errors[] = "Error deleting subject. Please try again.";
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

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>System Errors</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Subject Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" maxlength="4" value="<?php echo isset($_POST['subject_code']) ? htmlspecialchars($_POST['subject_code']) : ''; ?>" placeholder="Enter Subject Code">
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : ''; ?>" placeholder="Enter Subject Name">
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
                                            <form action="" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject['id']); ?>">
                                                <a href="edit.php?id=<?php echo htmlspecialchars($subject['id']); ?>" class="btn btn-sm btn-info">Edit</a>

                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
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
