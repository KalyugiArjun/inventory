<?php
$pageTitle = "Items - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin','storekeeper']);

// categories dropdown ke liye
$categories = [];
$catRes = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
if ($catRes) {
    while ($row = mysqli_fetch_assoc($catRes)) {
        $categories[] = $row;
    }
}

// Search / filter
$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$where = "1=1";
if ($search !== "") {
    $esc = mysqli_real_escape_string($conn, $search);
    $where .= " AND i.name LIKE '%$esc%'";
}
if ($categoryId > 0) {
    $where .= " AND i.category_id = $categoryId";
}

$sql = "SELECT i.*, c.name AS category_name
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE $where
        ORDER BY i.id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Inventory Items</div>
            <div class="card-subtitle">View, search and manage all college assets</div>
        </div>
        <a href="items_add.php" class="btn">+ Add Item</a>
    </div>

    <form method="GET" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
        <input 
            type="text" 
            name="q" 
            placeholder="Search by item name..." 
            value="<?php echo htmlspecialchars($search); ?>"
            style="flex:1; min-width:160px; padding:7px 9px; border-radius:999px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;">
        <select 
            name="category_id"
            style="min-width:160px; padding:7px 9px; border-radius:999px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" 
                    <?php echo $categoryId == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Filter</button>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:6px;">
            <tr>
                <th style="border:1px solid #1f2937; padding:6px;">ID</th>
                <th style="border:1px solid #1f2937; padding:6px;">Name</th>
                <th style="border:1px solid #1f2937; padding:6px;">Category</th>
                <th style="border:1px solid #1f2937; padding:6px;">Location</th>
                <th style="border:1px solid #1f2937; padding:6px;">Total</th>
                <th style="border:1px solid #1f2937; padding:6px;">Available</th>
                <th style="border:1px solid #1f2937; padding:6px;">Min Qty</th>
                <th style="border:1px solid #1f2937; padding:6px;">Actions</th>
            </tr>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $lowStock = ($row['quantity_available'] < $row['min_quantity']);
                ?>
                    <tr style="<?php echo $lowStock ? 'background:rgba(127,29,29,0.2);' : 'background:#020617;'; ?>">
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['id']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo htmlspecialchars($row['location']); ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['quantity_total']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;">
                            <?php if ($lowStock): ?>
                                <span style="color:#f97316; font-weight:600;"><?php echo $row['quantity_available']; ?></span>
                                <span style="font-size:11px; color:#fecaca;">(Low)</span>
                            <?php else: ?>
                                <?php echo $row['quantity_available']; ?>
                            <?php endif; ?>
                        </td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['min_quantity']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px; white-space:nowrap;">
                            <a href="item_edit.php?id=<?php echo $row['id']; ?>" class="btn" style="padding:4px 9px; font-size:12px;">Edit</a>
                            <a href="item_delete.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Delete this item?');"
                               class="btn-outline"
                               style="padding:4px 9px; font-size:12px; margin-left:4px;">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="border:1px solid #1f2937; padding:8px; text-align:center; color:#9ca3af;">
                        No items found. Click "Add Item" to create your first record.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
