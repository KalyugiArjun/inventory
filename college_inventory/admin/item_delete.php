<?php
require_once __DIR__ . "/../includes/auth.php";
require_role(['admin','storekeeper']);

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: items.php");
    exit;
}

$id = (int)$_GET['id'];

require_once __DIR__ . "/../includes/db.php";

// Simple delete – future me yahan check bhi laga sakte ho ki item issued to nahi hai
$sql = "DELETE FROM items WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: items.php");
exit;
