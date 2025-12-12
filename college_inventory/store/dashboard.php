<?php
$pageTitle = "Storekeeper Dashboard - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['storekeeper','admin']); // admin bhi dekh sakta
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Storekeeper Panel</div>
            <div class="card-subtitle">Issue/Return and physical stock management</div>
        </div>
        <span class="tag">Store</span>
    </div>
    <p style="font-size:13px; color:#cbd5f5;">
        Yahan se aap items ko students aur faculty ko issue / return karenge,
        stock update karenge aur low stock alerts dekhenge.
    </p>
</div>

<div class="card">
    <div class="card-title">Coming Modules</div>
    <p style="font-size:13px; color:#cbd5f5; margin-top:8px;">
        • Quick issue item form<br>
        • Pending returns list<br>
        • Category wise stock view
    </p>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
