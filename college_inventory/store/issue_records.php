<?php
$pageTitle = "Issue Records - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin','storekeeper']);

$status = isset($_GET['status']) ? $_GET['status'] : 'issued'; // default: only issued

$where = "1=1";
if (in_array($status, ['issued','returned','lost'])) {
    $where .= " AND ir.status = '$status'";
}

$sql = "SELECT ir.*, 
               i.name AS item_name,
               u1.name AS issued_to_name,
               u1.role AS issued_to_role,
               u2.name AS issued_by_name
        FROM issue_records ir
        JOIN items i ON ir.item_id = i.id
        JOIN users u1 ON ir.issued_to = u1.id
        JOIN users u2 ON ir.issued_by = u2.id
        WHERE $where
        ORDER BY ir.id DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Issue Records</div>
            <div class="card-subtitle">Track which items are issued, returned or lost</div>
        </div>
        <a href="issue_item.php" class="btn-outline">+ Issue New</a>
    </div>

    <div style="margin-bottom:10px;">
        <a href="?status=issued" class="tag" style="margin-right:4px; <?php echo $status=='issued'?'border-color:#f97316;color:#f97316;':''; ?>">Issued</a>
        <a href="?status=returned" class="tag" style="margin-right:4px; <?php echo $status=='returned'?'border-color:#f97316;color:#f97316;':''; ?>">Returned</a>
        <a href="?status=lost" class="tag" style="<?php echo $status=='lost'?'border-color:#f97316;color:#f97316;':''; ?>">Lost</a>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:6px;">
            <tr>
                <th style="border:1px solid #1f2937; padding:6px;">ID</th>
                <th style="border:1px solid #1f2937; padding:6px;">Item</th>
                <th style="border:1px solid #1f2937; padding:6px;">Issued To</th>
                <th style="border:1px solid #1f2937; padding:6px;">Qty</th>
                <th style="border:1px solid #1f2937; padding:6px;">Issue Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Due Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Return Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Status</th>
                <th style="border:1px solid #1f2937; padding:6px;">Action</th>
            </tr>
            <?php if ($res && mysqli_num_rows($res) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($res)): 
                    $isPending = ($row['status'] === 'issued');
                ?>
                    <tr style="background:#020617;">
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['id']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;">
                            <?php echo ucfirst($row['issued_to_role']); ?> - <?php echo htmlspecialchars($row['issued_to_name']); ?>
                        </td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['qty']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['issue_date']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['due_date']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['return_date']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;">
                            <?php if ($row['status'] === 'issued'): ?>
                                <span style="color:#f97316; font-weight:600;">Issued</span>
                            <?php elseif ($row['status'] === 'returned'): ?>
                                <span style="color:#22c55e;">Returned</span>
                            <?php else: ?>
                                <span style="color:#f87171;">Lost</span>
                            <?php endif; ?>
                        </td>
                        <td style="border:1px solid #1f2937; padding:6px; white-space:nowrap;">
                            <?php if ($isPending): ?>
                                <a href="return_item.php?id=<?php echo $row['id']; ?>"
                                   class="btn" style="padding:4px 9px; font-size:12px;"
                                   onclick="return confirm('Mark as returned?');">
                                   Mark Returned
                                </a>
                            <?php else: ?>
                                <span style="font-size:11px; color:#9ca3af;">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="border:1px solid #1f2937; padding:8px; text-align:center; color:#9ca3af;">
                        No records found.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
