<?php
require_once __DIR__ . "/../includes/auth.php";
require_role(['admin','storekeeper']);
require_once __DIR__ . "/../includes/db.php";

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: issue_records.php");
    exit;
}

$id = (int)$_GET['id'];

// record fetch
$res = mysqli_query($conn, "SELECT * FROM issue_records WHERE id = $id");
if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: issue_records.php");
    exit;
}
$rec = mysqli_fetch_assoc($res);

// only if status = issued
if ($rec['status'] !== 'issued') {
    header("Location: issue_records.php");
    exit;
}

$item_id = $rec['item_id'];
$qty     = $rec['qty'];

// item current qty
$itemRes = mysqli_query($conn, "SELECT quantity_available FROM items WHERE id = $item_id");
if ($itemRes && mysqli_num_rows($itemRes) === 1) {
    $item = mysqli_fetch_assoc($itemRes);
    $newAvail = $item['quantity_available'] + $qty;

    $today = date('Y-m-d');

    mysqli_query($conn, "UPDATE issue_records 
                         SET status='returned', return_date='$today'
                         WHERE id = $id");

    mysqli_query($conn, "UPDATE items 
                         SET quantity_available = $newAvail
                         WHERE id = $item_id");
}

header("Location: issue_records.php?status=issued");
exit;
