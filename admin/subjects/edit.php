<?php
require_once '../../functions.php';
require_once '../partials/header.php'; 


?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
            <?php require_once '../partials/side-bar.php'; ?>
        <!-- Edit Subject Content -->
        <div class="col-md-9">
            <div class="card-body">
            <div class="pt-5">
                <h3 class="card-title">Edit Subject</h3><br>
                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Edit Subject Form -->
            <div class="card mb-4">
                <div class="card-body">
                        <div class="mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code">
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../partials/footer.php'; ?>
