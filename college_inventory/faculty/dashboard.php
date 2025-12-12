<?php
$pageTitle = "Faculty Dashboard - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['faculty']);
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Faculty Panel</div>
            <div class="card-subtitle">Track your long-term assigned assets</div>
        </div>
        <span class="tag">Faculty</span>
    </div>
    <p style="font-size:13px; color:#cbd5f5;">
        Yahan se aap apne naam pe assigned assets (laptop, projector, etc.)
        aur unka status dekh sakenge. Baad me request features bhi add karenge.
    </p>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
