<?php
require_once '../../functions.php';
require_once '../partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

guard(); // Ensure the user is logged in

$errors = []; // Error messages array
$success_message = ''; // Success message

// Handle Add Student Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate input
    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    }
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($errors)) {
        $conn = connectDatabase();

        // Check for duplicate student ID
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Student ID already exists.";
        } else {
            // Insert new student
            $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $student_id, $first_name, $last_name);

            if ($stmt->execute()) {
                $success_message = "Student added successfully.";
                // Redirect to the same page to reset the form
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors[] = "Error adding student. Please try again.";
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

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10" style="margin-top: 30px;">
            <div class="card-body">
                <h3 class="card-title">Register a New Student</h3><br>

                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Register Student</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Error Messages -->
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

            <!-- Student Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID" value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </form>
                </div>
            </div>

            <!-- Student List -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student List</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody style="background-color: #d3d3d3; filter: brightness(110%);">
                            <?php
                            // Fetch students from the database
                            $conn = connectDatabase();
                            $result = $conn->query("SELECT * FROM students");

                            if ($result->num_rows > 0):
                                while ($student = $result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo urlencode($student['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="delete.php?id=<?php echo urlencode($student['id']); ?>" class="btn btn-sm btn-danger">Delete</a>
                                        <a href="attach-subject.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-sm btn-warning">Attach Subject</a>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center">No student records found.</td>
                                </tr>
                            <?php
                            endif;
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?> 
