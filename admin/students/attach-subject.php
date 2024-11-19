<?php
session_start();
require_once '../../functions.php';
require_once '../partials/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

guard(); // Ensure the user is logged in

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    echo "Student ID is missing or invalid."; // Debugging
    header("Location: register.php");
    exit;
}
echo "Student ID: " . htmlspecialchars($student_id) . "<br>"; // Debugging to verify value

// Ensure 'students' session exists
if (!isset($_SESSION['students'])) {
    echo "No students found in session.<br>"; // Debugging
    $_SESSION['students'] = [];
}

// Debugging: Print the entire students array from the session
echo "<pre>Current Students in Session: ";
var_dump($_SESSION['students']);
echo "</pre>";

// Retrieve the student data
$studentIndex = getSelectedStudentIndex($student_id);
if ($studentIndex === null) {
    echo "No student found for ID: " . htmlspecialchars($student_id) . "<br>"; // Debugging
    exit;
}
$studentData = getSelectedStudentData($studentIndex);
if (!$studentData) {
    echo "Student data could not be retrieved."; // Debugging
    header("Location: register.php");
    exit;
}
echo "<pre>Selected Student Data: ";
var_dump($studentData);
echo "</pre>";

$errors = [];
$successMessage = "";

// Handle the form submission for attaching subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSubjects = $_POST['subjects'] ?? [];

    // Validate the selected subjects
    $errors = validateAttachedSubject($selectedSubjects);

    if (empty($errors)) {
        // Ensure 'attached_subjects' is always an array
        $studentData['attached_subjects'] = $studentData['attached_subjects'] ?? [];
        
        // Add selected subjects to the student's attached subjects
        foreach ($selectedSubjects as $subject_code) {
            if (!in_array($subject_code, $studentData['attached_subjects'])) {
                $studentData['attached_subjects'][] = $subject_code;
            }
        }

        $_SESSION['students'][$studentIndex] = $studentData;
        $successMessage = "Subjects successfully attached to the student!";

        // Refresh the page to display the updated information
        header("Location: attach-subject.php?student_id=" . urlencode($student_id));
        exit;
    }
}

// Retrieve the list of available subjects (excluding already attached ones)
$availableSubjects = $_SESSION['subjects'] ?? [];
$attachedSubjects = $studentData['attached_subjects'] ?? [];

// Filter out subjects that are already attached
$subjectsToAttach = array_filter($availableSubjects, function($subject) use ($attachedSubjects) {
    return !in_array($subject['subject_code'], $attachedSubjects);
});
?>

<!-- HTML Content -->
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

<?php if ($successMessage): ?>
    <div class="alert alert-success"><?= htmlspecialchars($successMessage); ?></div>
<?php endif; ?>

<?php echo displayErrors($errors); ?>

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

        <form method="POST" action="attach-subject.php?student_id=<?= urlencode($student_id); ?>">
            <?php if (!empty($subjectsToAttach)): ?>
                <?php foreach ($subjectsToAttach as $subject): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="subjects[]" value="<?= htmlspecialchars($subject['subject_code']); ?>">
                        <label class="form-check-label">
                            <?= htmlspecialchars($subject['subject_code'] . " - " . $subject['subject_name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary mt-3">Attach Subjects</button>
            <?php else: ?>
                <p>No subjects available to attach.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card mt-5">
    <div class="card-body">
        <h5 class="card-title">Attached Subjects List</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody style="background-color: #d3d3d3; filter: brightness(110%);">
                <?php if (empty($studentData['attached_subjects'])): ?>
                    <tr>
                        <td colspan="3" class="text-center">No subjects attached yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studentData['attached_subjects'] as $subject_code): ?>
                        <?php
                        $subjectIndex = getSelectedSubjectIndex($subject_code);
                        $subject = getSelectedSubjectData($subjectIndex);
                        ?>
                        <?php if ($subject): ?>
                            <tr style="border-bottom: 1px solid #000;">
                                <td><?= htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <a href="dettach-subject.php?student_id=<?= urlencode($student_id); ?>&subject_code=<?= urlencode($subject['subject_code']); ?>" class="btn btn-sm btn-danger">Detach</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>

