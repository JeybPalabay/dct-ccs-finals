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
$query = "SELECT students.student_id, students.first_name, students.last_name, subjects.subject_code, subjects.subject_name FROM students_subjects 
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

// Handle detaching the subject when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deleteQuery = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    $deleteStmt = $connection->prepare($deleteQuery);
    $deleteStmt->bind_param("si", $student_id, $subject_id);

    if ($deleteStmt->execute()) {
        header("Location: attach-subject.php?student_id=" . urlencode($student_id));
        exit;
    } else {
        $errors[] = "Failed to detach subject.";
    }
}
?>

<div class="container-fluid">
    <div class="row">
            <?php require_once '../partials/side-bar.php'; // Include the sidebar ?>
        <div class="col-md-10">
            <div class="card-body"><br>
                <h3 class="card-title">Detach Subject from Student</h3><br>

                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                            <li class="breadcrumb-item"><a href="attach-subject.php?student_id=<?= urlencode($student_id); ?>">Attach Subject to Student</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Are you sure you want to detach the following subject from this student record?</p>

                    <ul>
                        <li><strong>Student ID:</strong> <?= htmlspecialchars($studentSubjectData['student_id']); ?></li>
                        <li><strong>First Name:</strong> <?= htmlspecialchars($studentSubjectData['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?= htmlspecialchars($studentSubjectData['last_name']); ?></li>
                        <li><strong>Subject Code:</strong> <?= htmlspecialchars($studentSubjectData['subject_code']); ?></li>
                        <li><strong>Subject Name:</strong> <?= htmlspecialchars($studentSubjectData['subject_name']); ?></li>
                    </ul>

                    <form method="POST" action="dettach-subject.php?student_id=<?= urlencode($student_id); ?>&subject_id=<?= urlencode($subject_id); ?>">
                        <a href="attach-subject.php?student_id=<?= urlencode($student_id); ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Detach Subject from Student</button>
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
