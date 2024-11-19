<?php
require_once '../../functions.php';
require_once '../partials/header.php';

guard(); // Ensure the user is authenticated

$student_id = $_GET['student_id'] ?? null;
$subject_id = $_GET['subject_id'] ?? null;

if (!$student_id || !$subject_id) {
    header("Location: register.php");
    exit;
}

$connection = connectDatabase();
if (!$connection || $connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Retrieve the student and subject data from the database
$query = "SELECT students.student_id, students.first_name, students.last_name, subjects.subject_code, subjects.subject_name, students_subjects.grade
          FROM students_subjects
          JOIN students ON students_subjects.student_id = students.student_id
          JOIN subjects ON students_subjects.subject_id = subjects.id
          WHERE students_subjects.student_id = ? AND students_subjects.subject_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("si", $student_id, $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$studentSubjectData = $result->fetch_assoc();

if (!$studentSubjectData) {
    header("Location: register.php");
    exit;
}

$errors = [];
$successMessage = "";

// Handle form submission to assign grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? null;

    // Validate the grade
    if (is_null($grade) || !is_numeric($grade) || $grade < 0 || $grade > 100) {
        $errors[] = "Please enter a valid grade between 0 and 100.";
    } else {
        // Update the grade in the database
        $updateQuery = "UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->bind_param("dsi", $grade, $student_id, $subject_id);

        if ($updateStmt->execute()) {
            $successMessage = "Grade assigned successfully!";
            // Refresh data to reflect the updated grade
            $studentSubjectData['grade'] = $grade;
        } else {
            $errors[] = "Failed to assign grade. Please try again.";
        }

        $updateStmt->close();
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../partials/side-bar.php'; ?>
        <div class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="card-body">
                <h3 class="card-title">Assign Grade to Subject</h3><br>
                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                            <li class="breadcrumb-item"><a href="attach-subject.php?student_id=<?= urlencode($student_id); ?>">Attach Subject to Student</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
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

            <!-- Student and Subject Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <p><strong>Selected Student and Subject Information:</strong></p>
                    <ul>
                        <li><strong>Student ID:</strong> <?= htmlspecialchars($studentSubjectData['student_id']); ?></li>
                        <li><strong>Name:</strong> <?= htmlspecialchars($studentSubjectData['first_name'] . ' ' . $studentSubjectData['last_name']); ?></li>
                        <li><strong>Subject Code:</strong> <?= htmlspecialchars($studentSubjectData['subject_code']); ?></li>
                        <li><strong>Subject Name:</strong> <?= htmlspecialchars($studentSubjectData['subject_name']); ?></li>
                    </ul>

                    <!-- Grade Form -->
                    <form method="POST" action="assign-grade.php?student_id=<?= urlencode($student_id); ?>&subject_id=<?= urlencode($subject_id); ?>">
                        <div class="mb-3">
                            <label for="grade" class="form-label">Grade</label>
                            <input type="number" class="form-control" id="grade" name="grade" value="<?= htmlspecialchars($studentSubjectData['grade']); ?>" min="0" max="100" step="0.01">
                        </div>
                        <a href="attach-subject.php?student_id=<?= urlencode($student_id); ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Assign Grade to Subject</button>
                    </form>
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
