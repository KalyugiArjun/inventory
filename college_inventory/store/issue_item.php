<?php
$pageTitle = "Issue Items - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin','storekeeper']);

// Items (sirf jinke paas available qty > 0)
$itemSql = "SELECT i.*, c.name AS category_name
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.quantity_available > 0
            ORDER BY i.name";
$itemRes = mysqli_query($conn, $itemSql);
$items = [];
if ($itemRes) {
    while ($row = mysqli_fetch_assoc($itemRes)) {
        $items[] = $row;
    }
}

// Students + Faculty list
$userSql = "SELECT id, name, role, department 
            FROM users 
            WHERE role IN ('student','faculty')
            ORDER BY role, name";
$userRes = mysqli_query($conn, $userSql);
$usersList = [];
if ($userRes) {
    while ($row = mysqli_fetch_assoc($userRes)) {
        $usersList[] = $row;
    }
}

$message = "";

if (isset($_POST['issue'])) {
    $item_id   = (int)$_POST['item_id'];
    $issued_to = (int)$_POST['issued_to'];
    $qty       = (int)$_POST['qty'];
    $due_date  = $_POST['due_date'] !== "" ? $_POST['due_date'] : null;
    $issued_by = $user['id'];

    // item details
    $checkRes = mysqli_query($conn, "SELECT * FROM items WHERE id = $item_id");
    if ($checkRes && mysqli_num_rows($checkRes) === 1) {
        $itemRow = mysqli_fetch_assoc($checkRes);

        if ($qty <= 0) {
            $message = "Quantity should be greater than 0.";
        } elseif ($qty > $itemRow['quantity_available']) {
            $message = "Not enough quantity available. Current available: " . $itemRow['quantity_available'];
        } else {
            // insert issue record
            $today = date('Y-m-d');
            $duePart = $due_date ? "'$due_date'" : "NULL";

            $sqlIssue = "INSERT INTO issue_records 
                (item_id, issued_to, issued_by, issue_date, due_date, qty, status)
                VALUES
                ($item_id, $issued_to, $issued_by, '$today', $duePart, $qty, 'issued')";
            
            // update available quantity
            $newAvail = $itemRow['quantity_available'] - $qty;
            $sqlUpdate = "UPDATE items SET quantity_available = $newAvail WHERE id = $item_id";

            if (mysqli_query($conn, $sqlIssue) && mysqli_query($conn, $sqlUpdate)) {
                $message = "Item issued successfully!";
            } else {
                $message = "Database error while issuing item.";
            }
        }
    } else {
        $message = "Selected item not found.";
    }
}
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Issue Item</div>
            <div class="card-subtitle">Issue equipment/books to students or faculty</div>
        </div>
        <a href="issue_records.php" class="btn-outline">View Records</a>
    </div>

    <?php if ($message): ?>
        <p style="margin-bottom:10px; color:#f97316; font-size:13px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <p style="font-size:13px; color:#cbd5f5;">No items with available quantity found. Please add items or update stock.</p>
    <?php else: ?>
        <form method="POST" style="display:grid; gap:10px; max-width:520px;">
            <div>
                <label style="font-size:13px;">Select Item *</label>
                <select name="item_id"
                        style="width:100%; padding:8px; border-radius:10px; border:1px solid #1f2937; background:#020617; color:#e5e7eb;">
                    <?php foreach ($items as $it): ?>
                        <option value="<?php echo $it['id']; ?>">
                            <?php echo htmlspecialchars($it['name']); ?>
                            (<?php echo htmlspecialchars($it['category_name']); ?> - Avl: <?php echo $it['quantity_available']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="font-size:13px;">Issue To (Student/Faculty) *</label>
                <select name="issued_to"
                        style="width:100%; padding:8px; border-radius:10px; border:1px solid #1f2937; background:#020617; color:#e5e7eb;">
                    <?php foreach ($usersList as $u): ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo ucfirst($u['role']); ?> - <?php echo htmlspecialchars($u['name']); ?>
                            (<?php echo htmlspecialchars($u['department']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:8px;">
                <div>
                    <label style="font-size:13px;">Quantity *</label>
                    <input type="number" name="qty" min="1" value="1" required>
                </div>
                <div>
                    <label style="font-size:13px;">Due Date (optional)</label>
                    <input type="date" name="due_date">
                </div>
            </div>

            <button type="submit" name="issue" class="btn" style="margin-top:6px; max-width:180px;">
                Issue Item
            </button>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
