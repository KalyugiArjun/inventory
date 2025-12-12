<?php
$host = "localhost";
$user = "root";      // yahan apna MySQL user
$pass = "";          // agar password hai to yahan daalo
$dbname = "college_inventory_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
