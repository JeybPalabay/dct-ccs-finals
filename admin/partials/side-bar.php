<?php
require_once 'header.php';
require_once __DIR__ . '/../../functions.php';

// Get the current page and directory
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary vh-100">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Company name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <!-- Dashboard Link -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php echo $currentPage === 'dashboard.php' ? 'active fw-bold' : ''; ?>" href="/admin/dashboard.php">
                        <i class="fa-solid fa-gauge fa-fw me-2"></i>
                        Dashboard
                    </a>
                </li>
                <!-- Subjects Link -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php echo $currentDir === 'subjects' ? 'active fw-bold' : ''; ?>" href="/admin/subjects/add.php">
                        <i class="fa-solid fa-book fa-fw me-2"></i>
                        Subjects
                    </a>
                </li>
                <!-- Students Link -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php echo $currentPage === 'register.php' || $currentDir === 'students' ? 'active fw-bold' : ''; ?>" href="/admin/students/register.php">
                        <i class="fa-solid fa-user fa-fw me-2"></i>
                        Students
                    </a>
                </li>

           
            <hr class="my-3">

            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="../../index.php">
                        <i class="fa-solid fa-right-to-bracket fa-fw me-2"></i>                        
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
