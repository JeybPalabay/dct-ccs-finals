<?php

require_once __DIR__ . '/../../functions.php'; // Correct path to functions.php
require_once __DIR__ . '/../partials/header.php'; // Correct path to header.php
require_once __DIR__ . '/../partials/side-bar.php'; // Correct path to side-bar.php

guard(); // Ensure the user is authenticated

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
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




<?php
require_once __DIR__ . '/../partials/footer.php'; // Correct path to footer.php
?>
