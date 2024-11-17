<?php

require_once '../partials/header.php'; // Adjust path if needed
require_once '../../functions.php';



?>

<div class="container-fluid">
    <div class="row">
            <?php require_once '../partials/side-bar.php'; ?>
      

      

        <!-- Main Content Column -->
        <div class="col-md-9 col-lg-10">
            <!-- Main Content -->
            <div class="pt-5">
                <h3 class="card-title">Add a New Subject</h3>

                <div class="p-3 rounded border mb-3" style="background-color: #d3d3d3; filter: brightness(110%);">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Subject Form Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="add.php" method="POST">
                        <div class="mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code" value="<?php echo isset($subject_data['subject_code']) ? htmlspecialchars($subject_data['subject_code']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name" value="<?php echo isset($subject_data['subject_name']) ? htmlspecialchars($subject_data['subject_name']) : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>

            <!-- Subject List Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Subject List</h5>

                    <!-- Subject Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: #d3d3d3; filter: brightness(110%);">
                                <!-- Add subject list data rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
require_once '../partials/footer.php';
?>  
