<?php
require_once 'partials/header.php';
require_once '../functions.php';
require_once 'partials/side-bar.php';

guard(); // Ensure the user is authenticated

// Connect to the database and fetch counts
$connection = connectDatabase();

// Query to count the number of subjects
$querySubjects = "SELECT COUNT(*) AS total_subjects FROM subjects";
$resultSubjects = $connection->query($querySubjects);
$subjectsCount = $resultSubjects->fetch_assoc()['total_subjects'];

// Query to count the number of students
$queryStudents = "SELECT COUNT(*) AS total_students FROM students";
$resultStudents = $connection->query($queryStudents);
$studentsCount = $resultStudents->fetch_assoc()['total_students'];

// Close the database connection
$connection->close();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        
    
    <div class="row mt-5">
        <!-- Number of Subjects -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo htmlspecialchars($subjectsCount); ?></h5>
                </div>
            </div>
        </div>
        <!-- Number of Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?php echo htmlspecialchars($studentsCount); ?></h5>
                </div>
            </div>
        </div>
        <!-- Number of Failed Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title">0</h5>
                </div>
            </div>
        </div>
        <!-- Number of Passed Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title">0</h5>
                </div>
            </div>
        </div>
    </div>    
</main>  
