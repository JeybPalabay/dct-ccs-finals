<?php
require_once '../../functions.php';
require_once '../partials/header.php';

guard(); // Ensure the user is authenticated

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    header("Location: register.php");
    exit;
}

$connection = connectDatabase();
if (!$connection || $connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Retrieve the student data from the database
$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();

if (!$studentData) {
    header("Location: register.php");
    exit;
}

$errors = [];
$successMessage = "";

// Handle the form submission for attaching subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSubjects = $_POST['subjects'] ?? [];

    if (empty($selectedSubjects)) {
        $errors[] = "Please select at least one subject to attach.";
    } else {
        // Attach the selected subjects to the student
        foreach ($selectedSubjects as $subject_id) {
            // Check if the subject is already attached to the student
            $checkQuery = "SELECT * FROM students_subjects WHERE student_id = ? AND subject_id = ?";
            $checkStmt = $connection->prepare($checkQuery);
            $checkStmt->bind_param("si", $student_id, $subject_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                // Insert new subject if it's not already attached
                $insertQuery = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
                $insertStmt = $connection->prepare($insertQuery);
                if ($insertStmt) {
                    $defaultGrade = 0; // Set default grade value
                    $insertStmt->bind_param("sii", $student_id, $subject_id, $defaultGrade);
                    if ($insertStmt->execute()) {
                        $successMessage = "Subjects successfully attached to the student!";
                    } else {
                        $errors[] = "Failed to attach subject ID: " . htmlspecialchars($subject_id);
                    }
                } else {
                    $errors[] = "Failed to prepare the query for subject ID: " . htmlspecialchars($subject_id);
                }
            }
        }

        // Refresh the page to display the updated information
        header("Location: attach-subject.php?student_id=" . urlencode($student_id));
        exit;
    }
}

// Retrieve the list of available subjects (excluding already attached ones)
$query = "SELECT * FROM subjects WHERE id NOT IN (SELECT subject_id FROM students_subjects WHERE student_id = ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$availableSubjects = $stmt->get_result();

// Retrieve the list of attached subjects with their codes
$query = "
    SELECT 
        subjects.subject_code, 
        subjects.subject_name, 
        students_subjects.subject_id, 
        students_subjects.grade
    FROM 
        subjects
    JOIN 
        students_subjects ON subjects.id = students_subjects.subject_id
    WHERE 
        students_subjects.student_id = ?
";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$attachedSubjects = $stmt->get_result();
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../partials/side-bar.php'; // Include the sidebar ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-md-4 pt-4">
            <!-- Page Header -->
            <div class="card-body">
                <h3 class="card-title">Attach Subject to Student</h3><br>
                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Success Message -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Student Information -->
            <div class="card">
                <div class="card-body">
                    <div>
                        <strong>Selected Student Information:</strong>
                        <ul>
                            <li>Student ID: <?= htmlspecialchars($studentData['student_id']); ?></li>
                            <li>Name: <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']); ?></li>
                        </ul>
                    </div>

                    <hr>

                    <!-- Attach Subjects Form -->
                    <form method="POST" action="attach-subject.php?student_id=<?= urlencode($student_id); ?>">
                        <p><strong>Select Subjects to Attach:</strong></p>
                        <?php if ($availableSubjects->num_rows > 0): ?>
                            <?php while ($subject = $availableSubjects->fetch_assoc()): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject['id']); ?>">
                                    <label class="form-check-label">
                                        <?= htmlspecialchars($subject['subject_code'] . " - " . $subject['subject_name']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                            <button type="submit" class="btn btn-primary mt-3">Attach Subjects</button>
                        <?php else: ?>
                            <p>No subjects available to attach.</p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Attached Subjects List -->
            <div class="card mt-5">
                <div class="card-body">
                    <h5 class="card-title">Attached Subjects List</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Grade</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody style="background-color: #d3d3d3; filter: brightness(110%);">
                            <?php if ($attachedSubjects->num_rows == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No subjects attached yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($subject = $attachedSubjects->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td><?= htmlspecialchars($subject['subject_code']); ?></td> <!-- Display subject code -->
                                        <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                                        <td>
                                            <?= ($subject['grade'] == 0) ? '--.--' : htmlspecialchars($subject['grade']); ?> <!-- Show "--.--" for grade = 0 -->
                                        </td>
                                        <td>
                                            <a href="dettach-subject.php?student_id=<?= urlencode($student_id); ?>&subject_id=<?= urlencode($subject['subject_id']); ?>" class="btn btn-sm btn-danger">Detach Subject</a>
                                            <a href="assign-grade.php?student_id=<?= urlencode($student_id); ?>&subject_id=<?= urlencode($subject['subject_id']); ?>" class="btn btn-sm btn-success">Assign Grade</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>

<?php
// Close the database connection
$connection->close();
?>
