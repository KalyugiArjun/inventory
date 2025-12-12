<?php
$pageTitle = "Edit Item - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin','storekeeper']);

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: items.php");
    exit;
}

$id = (int)$_GET['id'];

// Categories list
$categories = [];
$catRes = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
if ($catRes) {
    while ($row = mysqli_fetch_assoc($catRes)) {
        $categories[] = $row;
    }
}

$message = "";

// Current item data
$itemRes = mysqli_query($conn, "SELECT * FROM items WHERE id = $id");
if (!$itemRes || mysqli_num_rows($itemRes) === 0) {
    ?>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Item Not Found</div>
        </div>
        <p style="font-size:13px; color:#cbd5f5;">This item record does not exist.</p>
        <a href="items.php" class="btn" style="margin-top:8px;">← Back to Items</a>
    </div>
    <?php
    require_once __DIR__ . "/../includes/footer.php";
    exit;
}
$item = mysqli_fetch_assoc($itemRes);

// Update logic
if (isset($_POST['update'])) {
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

        $sql = "UPDATE items SET
                    name = '$name',
                    category_id = $category_id,
                    quantity_total = $qty_total,
                    quantity_available = $qty_avail,
                    min_quantity = $min_qty,
                    unit_price = $unit_price,
                    location = '$location'
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            $message = "Item updated successfully!";
            // refresh item data
            $itemRes = mysqli_query($conn, "SELECT * FROM items WHERE id = $id");
            $item = mysqli_fetch_assoc($itemRes);
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Edit Item (#<?php echo $id; ?>)</div>
            <div class="card-subtitle">Update details carefully according to stock register</div>
        </div>
        <a href="items.php" class="btn-outline">← Back to Items</a>
    </div>

    <?php if ($message): ?>
        <p style="margin-bottom:10px; color:#f97316; font-size:13px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" style="display:grid; gap:10px; max-width:520px;">
        <div>
            <label style="font-size:13px;">Item Name *</label>
            <input type="text" name="name" required
                   value="<?php echo htmlspecialchars($item['name']); ?>">
        </div>

        <div>
            <label style="font-size:13px;">Category</label>
            <select name="category_id"
                    style="width:100%; padding:8px; border-radius:10px; border:1px solid #1f2937; background:#020617; color:#e5e7eb;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo ($item['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:8px;">
            <div>
                <label style="font-size:13px;">Total Quantity *</label>
                <input type="number" name="quantity_total" min="0" required
                       value="<?php echo (int)$item['quantity_total']; ?>">
            </div>
            <div>
                <label style="font-size:13px;">Available Quantity *</label>
                <input type="number" name="quantity_available" min="0" required
                       value="<?php echo (int)$item['quantity_available']; ?>">
            </div>
            <div>
                <label style="font-size:13px;">Min Quantity (Alert)</label>
                <input type="number" name="min_quantity" min="1"
                       value="<?php echo (int)$item['min_quantity']; ?>">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:8px;">
            <div>
                <label style="font-size:13px;">Unit Price (optional)</label>
                <input type="number" step="0.01" name="unit_price"
                       value="<?php echo htmlspecialchars($item['unit_price']); ?>">
            </div>
            <div>
                <label style="font-size:13px;">Location (Lab/Room)</label>
                <input type="text" name="location"
                       value="<?php echo htmlspecialchars($item['location']); ?>"
                       placeholder="e.g. CSE Lab-1">
            </div>
        </div>

        <button type="submit" name="update" class="btn" style="margin-top:6px; max-width:180px;">
            Update Item
        </button>
    </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
