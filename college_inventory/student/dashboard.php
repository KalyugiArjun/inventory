<?php
$pageTitle = "Student Dashboard - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['student']);
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Student Panel</div>
            <div class="card-subtitle">View your issued items & raise requests</div>
        </div>
        <span class="tag">Student</span>
    </div>
    <p style="font-size:13px; color:#cbd5f5;">
        Aap yahan se lab equipment / books ke issue requests raise kar sakenge
        aur apne issued items ka history dekh sakenge.
    </p>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
