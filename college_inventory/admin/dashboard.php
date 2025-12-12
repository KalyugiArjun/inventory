<?php
$pageTitle = "Admin Dashboard - College Inventory";
require_once __DIR__ . "/../includes/header.php";
require_role(['admin']);

// 1) Simple stats
$totalItems = 0;
$totalUsers = 0;
$totalIssued = 0;

$res1 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM items");
if ($res1) $totalItems = (int)mysqli_fetch_assoc($res1)['c'];

$res2 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
if ($res2) $totalUsers = (int)mysqli_fetch_assoc($res2)['c'];

$res3 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM issue_records WHERE status='issued'");
if ($res3) $totalIssued = (int)mysqli_fetch_assoc($res3)['c'];

// 2) Low stock (available < min_quantity)
$lowStockCount = 0;
$lowSql = "SELECT COUNT(*) AS c FROM items WHERE quantity_available < min_quantity";
$lowRes = mysqli_query($conn, $lowSql);
if ($lowRes) $lowStockCount = (int)mysqli_fetch_assoc($lowRes)['c'];

// 3) Category wise counts
$catStats = [];
$catSql = "SELECT c.name AS category_name, COUNT(i.id) AS total_items
           FROM categories c
           LEFT JOIN items i ON i.category_id = c.id
           GROUP BY c.id, c.name
           ORDER BY c.name";
$catRes = mysqli_query($conn, $catSql);
if ($catRes) {
    while ($row = mysqli_fetch_assoc($catRes)) {
        $catStats[] = $row;
    }
}

// 4) Recent 5 issue records
$recentIssues = [];
$recentSql = "SELECT ir.*, 
                     i.name AS item_name,
                     u1.name AS issued_to_name,
                     u1.role AS issued_to_role
              FROM issue_records ir
              JOIN items i ON ir.item_id = i.id
              JOIN users u1 ON ir.issued_to = u1.id
              ORDER BY ir.id DESC
              LIMIT 5";
$recentRes = mysqli_query($conn, $recentSql);
if ($recentRes) {
    while ($row = mysqli_fetch_assoc($recentRes)) {
        $recentIssues[] = $row;
    }
}
?>

<!-- WELCOME CARD -->
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</div>
            <div class="card-subtitle">
                You are logged in as <strong>Admin</strong>. Monitor complete college inventory from this dashboard.
            </div>
        </div>
        <button class="btn-outline" disabled>Admin Panel</button>
    </div>
</div>

<!-- TOP STATS -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Overview</div>
        <div class="card-subtitle">Quick snapshot of your inventory system</div>
    </div>
    <div class="grid grid-3">
        <div class="stat">
            <div class="stat-label">Total Items</div>
            <div class="stat-value"><?php echo $totalItems; ?></div>
            <div class="stat-hint">All categories combined</div>
        </div>
        <div class="stat">
            <div class="stat-label">Registered Users</div>
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-hint">Admins, Store, Students, Faculty</div>
        </div>
        <div class="stat">
            <div class="stat-label">Currently Issued</div>
            <div class="stat-value"><?php echo $totalIssued; ?></div>
            <div class="stat-hint">Items not yet returned</div>
        </div>
    </div>
</div>

<!-- LOW STOCK + CATEGORY WISE -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Stock Health</div>
        <div class="card-subtitle">Low stock items & category-wise distribution</div>
    </div>

    <div class="grid" style="gap:14px;">
        <!-- Low stock summary -->
        <div class="stat">
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value">
                <?php echo $lowStockCount; ?>
            </div>
            <div class="stat-hint">
                Items where <b>Available &lt; Min Quantity</b>.<br>
                <span style="font-size:11px;">Check details below.</span>
            </div>
        </div>

        <!-- Category wise table -->
        <div style="border-radius:14px; border:1px solid rgba(30,64,175,0.7); padding:10px;">
            <div style="font-size:12px; color:#9ca3af; margin-bottom:6px;">Category-wise Items Count</div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <tr>
                        <th style="border:1px solid #1f2937; padding:5px;">Category</th>
                        <th style="border:1px solid #1f2937; padding:5px; text-align:right;">Items</th>
                    </tr>
                    <?php if (!empty($catStats)): ?>
                        <?php foreach ($catStats as $c): ?>
                            <tr>
                                <td style="border:1px solid #1f2937; padding:5px;">
                                    <?php echo htmlspecialchars($c['category_name']); ?>
                                </td>
                                <td style="border:1px solid #1f2937; padding:5px; text-align:right;">
                                    <?php echo $c['total_items']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="border:1px solid #1f2937; padding:6px; text-align:center; color:#9ca3af;">
                                No categories / items found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed low stock list -->
    <div style="margin-top:12px;">
        <div style="font-size:12px; color:#9ca3af; margin-bottom:4px;">
            Low Stock Items (top 5)
        </div>
        <?php
        $lowListSql = "SELECT i.*, c.name AS category_name
                       FROM items i
                       LEFT JOIN categories c ON i.category_id = c.id
                       WHERE i.quantity_available < i.min_quantity
                       ORDER BY (i.min_quantity - i.quantity_available) DESC
                       LIMIT 5";
        $lowListRes = mysqli_query($conn, $lowListSql);
        ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <tr>
                    <th style="border:1px solid #1f2937; padding:5px;">Item</th>
                    <th style="border:1px solid #1f2937; padding:5px;">Category</th>
                    <th style="border:1px solid #1f2937; padding:5px;">Available</th>
                    <th style="border:1px solid #1f2937; padding:5px;">Min Qty</th>
                    <th style="border:1px solid #1f2937; padding:5px;">Location</th>
                </tr>
                <?php if ($lowListRes && mysqli_num_rows($lowListRes) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($lowListRes)): ?>
                        <tr style="background:rgba(127,29,29,0.25);">
                            <td style="border:1px solid #1f2937; padding:5px;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>
                            <td style="border:1px solid #1f2937; padding:5px;">
                                <?php echo htmlspecialchars($row['category_name']); ?>
                            </td>
                            <td style="border:1px solid #1f2937; padding:5px; color:#f97316; font-weight:600;">
                                <?php echo $row['quantity_available']; ?>
                            </td>
                            <td style="border:1px solid #1f2937; padding:5px;">
                                <?php echo $row['min_quantity']; ?>
                            </td>
                            <td style="border:1px solid #1f2937; padding:5px;">
                                <?php echo htmlspecialchars($row['location']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="border:1px solid #1f2937; padding:6px; text-align:center; color:#9ca3af;">
                            No low stock items right now. Stock is healthy ✅
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- RECENT ISSUES -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Recent Issue Activity</div>
        <div class="card-subtitle">Last 5 issue transactions in the system</div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <tr>
                <th style="border:1px solid #1f2937; padding:5px;">ID</th>
                <th style="border:1px solid #1f2937; padding:5px;">Item</th>
                <th style="border:1px solid #1f2937; padding:5px;">Issued To</th>
                <th style="border:1px solid #1f2937; padding:5px;">Qty</th>
                <th style="border:1px solid #1f2937; padding:5px;">Issue Date</th>
                <th style="border:1px solid #1f2937; padding:5px;">Due</th>
                <th style="border:1px solid #1f2937; padding:5px;">Status</th>
            </tr>
            <?php if (!empty($recentIssues)): ?>
                <?php foreach ($recentIssues as $r): ?>
                    <tr style="background:#020617;">
                        <td style="border:1px solid #1f2937; padding:5px;"><?php echo $r['id']; ?></td>
                        <td style="border:1px solid #1f2937; padding:5px;"><?php echo htmlspecialchars($r['item_name']); ?></td>
                        <td style="border:1px solid #1f2937; padding:5px;">
                            <?php echo ucfirst($r['issued_to_role']); ?> - <?php echo htmlspecialchars($r['issued_to_name']); ?>
                        </td>
                        <td style="border:1px solid #1f2937; padding:5px;"><?php echo $r['qty']; ?></td>
                        <td style="border:1px solid #1f2937; padding:5px;"><?php echo $r['issue_date']; ?></td>
                        <td style="border:1px solid #1f2937; padding:5px;"><?php echo $r['due_date']; ?></td>
                        <td style="border:1px solid #1f2937; padding:5px;">
                            <?php if ($r['status'] === 'issued'): ?>
                                <span style="color:#f97316; font-weight:600;">Issued</span>
                            <?php elseif ($r['status'] === 'returned'): ?>
                                <span style="color:#22c55e;">Returned</span>
                            <?php else: ?>
                                <span style="color:#f87171;">Lost</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="border:1px solid #1f2937; padding:6px; text-align:center; color:#9ca3af;">
                        No issue activity found yet.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- NEXT STEPS / HELP CARD -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Next Steps for Project Submission</div>
    </div>
    <p style="font-size:13px; color:#cbd5f5; margin-bottom:6px;">
        • Take screenshots of this dashboard for your report (Overview, Stock Health, Recent Issues).<br>
        • In viva, you can explain how low stock alert and issue tracking works in real college scenario.<br>
        • You can also add Reports & User Management modules as future scope.
    </p>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
