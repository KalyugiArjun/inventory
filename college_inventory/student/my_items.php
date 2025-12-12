<?php
$pageTitle = "My Items - Student";
require_once __DIR__ . "/../includes/header.php";
require_role(['student']);

$uid = $user['id'];

$sql = "SELECT ir.*, 
               i.name AS item_name,
               i.location,
               i.category_id,
               c.name AS category_name
        FROM issue_records ir
        JOIN items i ON ir.item_id = i.id
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE ir.issued_to = $uid
        ORDER BY ir.id DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">My Issued Items</div>
            <div class="card-subtitle">All equipment / books issued on your name</div>
        </div>
        <span class="tag">Student</span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:6px;">
            <tr>
                <th style="border:1px solid #1f2937; padding:6px;">ID</th>
                <th style="border:1px solid #1f2937; padding:6px;">Item</th>
                <th style="border:1px solid #1f2937; padding:6px;">Category</th>
                <th style="border:1px solid #1f2937; padding:6px;">Qty</th>
                <th style="border:1px solid #1f2937; padding:6px;">Issue Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Due Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Return Date</th>
                <th style="border:1px solid #1f2937; padding:6px;">Status</th>
            </tr>

            <?php if ($res && mysqli_num_rows($res) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                    <tr style="background:#020617;">
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo $row['id']; ?></td>
                        <td style="border:1px solid #1f2937; padding:6px;">
                            <?php echo htmlspecialchars($row['item_name']); ?>
                            <div style="font-size:11px; color:#9ca3af;">
                                <?php echo htmlspecialchars($row['location']); ?>
                            </div>
                        </td>
                        <td style="border:1px solid #1f2937; padding:6px;"><?php echo htmlspecialchars($row['category_name']); ?></td>
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
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="border:1px solid #1f2937; padding:8px; text-align:center; color:#9ca3af;">
                        No items found on your name.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
