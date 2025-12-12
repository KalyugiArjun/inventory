<?php
$pageTitle = "Add Item - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin','storekeeper']);

// categories dropdown
$categories = [];
$catRes = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
if ($catRes) {
    while ($row = mysqli_fetch_assoc($catRes)) {
        $categories[] = $row;
    }
}

$message = "";

if (isset($_POST['save'])) {
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $qty_total   = (int)$_POST['quantity_total'];
    $qty_avail   = (int)$_POST['quantity_available'];
    $min_qty     = (int)$_POST['min_quantity'];
    $unit_price  = (float)$_POST['unit_price'];
    $location    = mysqli_real_escape_string($conn, $_POST['location']);

    if ($name === "") {
        $message = "Item name is required.";
    } else {
        if ($qty_avail > $qty_total) $qty_avail = $qty_total;

        $sql = "INSERT INTO items 
                (name, category_id, quantity_total, quantity_available, min_quantity, unit_price, location)
                VALUES
                ('$name', $category_id, $qty_total, $qty_avail, $min_qty, $unit_price, '$location')";
        if (mysqli_query($conn, $sql)) {
            $message = "Item added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Add New Item</div>
            <div class="card-subtitle">Fill details carefully as per college record</div>
        </div>
        <a href="items.php" class="btn-outline">← Back to Items</a>
    </div>

    <?php if ($message): ?>
        <p style="margin-bottom:10px; color:#f97316; font-size:13px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" style="display:grid; gap:10px; max-width:520px;">
        <div>
            <label style="font-size:13px;">Item Name *</label>
            <input type="text" name="name" required placeholder="e.g. Dell Monitor 22 inch">
        </div>

        <div>
            <label style="font-size:13px;">Category</label>
            <select name="category_id"
                    style="width:100%; padding:8px; border-radius:10px; border:1px solid #1f2937; background:#020617; color:#e5e7eb;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:8px;">
            <div>
                <label style="font-size:13px;">Total Quantity *</label>
                <input type="number" name="quantity_total" min="0" value="1" required>
            </div>
            <div>
                <label style="font-size:13px;">Available Quantity *</label>
                <input type="number" name="quantity_available" min="0" value="1" required>
            </div>
            <div>
                <label style="font-size:13px;">Min Quantity (Alert)</label>
                <input type="number" name="min_quantity" min="1" value="1">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:8px;">
            <div>
                <label style="font-size:13px;">Unit Price (optional)</label>
                <input type="number" step="0.01" name="unit_price" placeholder="0.00">
            </div>
            <div>
                <label style="font-size:13px;">Location (Lab/Room)</label>
                <input type="text" name="location" placeholder="e.g. CSE Lab-1">
            </div>
        </div>

        <button type="submit" name="save" class="btn" style="margin-top:6px; max-width:160px;">
            Save Item
        </button>
    </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
