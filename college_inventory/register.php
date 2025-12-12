<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$message = "";
$message_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form se data uthao
    $name       = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email      = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $department = mysqli_real_escape_string($conn, $_POST['department'] ?? '');
    $password   = mysqli_real_escape_string($conn, $_POST['password'] ?? '');

    // Basic validation
    if ($name === '' || $email === '' || $department === '' || $password === '') {
        $message = "Please fill all fields.";
        $message_type = "error";
    } else {
        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email='$email' LIMIT 1";
        $checkRes = mysqli_query($conn, $checkSql);

        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $message = "This email is already registered. Try another or login.";
            $message_type = "error";
        } else {
            // Insert new student user
            // NOTE: password plain text hai because tumhara login bhi plain text use kar raha hai
            $insertSql = "INSERT INTO users (name, email, password, role, department)
                          VALUES ('$name', '$email', '$password', 'student', '$department')";

            if (mysqli_query($conn, $insertSql)) {
                $message = "Registration successful! You can now login.";
                $message_type = "success";
            } else {
                $message = "Something went wrong while saving data.";
                $message_type = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration - College Inventory</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --bg: #020617;
    --accent: #f97316;
    --accent-soft: #fb923c;
    --text: #e5e7eb;
    --muted: #9ca3af;
    --border: #1f2937;
}

*{margin:0;padding:0;box-sizing:border-box;font-family:system-ui,Segoe UI;}

body{
    background: radial-gradient(circle at top,#111827 0,#020617 45%,#000 100%);
    background-size:200% 200%;
    animation:bgMove 16s ease-in-out infinite;
    color:var(--text);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

@keyframes bgMove{
    0%{background-position:0 0;}
    50%{background-position:100% 70%;}
    100%{background-position:0 0;}
}

.register-box{
    width:100%;
    max-width:420px;
    padding:28px;
    border-radius:20px;
    border:1px solid rgba(148,163,184,.35);
    background:rgba(15,23,42,0.85);
    backdrop-filter:blur(10px);
    animation:slideIn .55s ease-out;
    box-shadow:0 25px 60px rgba(0,0,0,.85);
}

@keyframes slideIn{
    from{opacity:0;transform:translateX(60px);}
    to{opacity:1;transform:translateX(0);}
}

.title{
    text-align:center;
    font-size:20px;
    margin-bottom:6px;
    font-weight:700;
}
.subtitle{
    text-align:center;
    font-size:12px;
    color:var(--muted);
    margin-bottom:16px;
}

input{
    width:100%;
    padding:10px 12px;
    margin-bottom:12px;
    border:none;
    border-radius:10px;
    font-size:14px;
    background:#0d1323;
    color:var(--text);
    border:1px solid var(--border);
}
input:focus{
    outline:none;
    border-color:var(--accent-soft);
    box-shadow:0 0 5px rgba(249,115,22,.3);
}

.btn{
    width:100%;
    padding:10px;
    font-size:14px;
    font-weight:600;
    border:none;
    border-radius:999px;
    background:var(--accent);
    color:#000;
    cursor:pointer;
    transition:.25s;
}
.btn:hover{
    transform:translateY(-2px);
    background:var(--accent-soft);
    box-shadow:0 8px 20px rgba(249,115,22,.45);
}

.link{
    text-align:center;
    font-size:12px;
    margin-top:12px;
}
.link a{
    color:var(--accent-soft);
    text-decoration:none;
}

/* message styles */
.alert{
    font-size:12px;
    margin-bottom:10px;
    padding:7px 9px;
    border-radius:10px;
    text-align:center;
}
.alert.error{
    background:rgba(248,113,113,.12);
    border:1px solid rgba(248,113,113,.6);
    color:#fecaca;
}
.alert.success{
    background:rgba(22,163,74,.12);
    border:1px solid rgba(22,163,74,.7);
    color:#bbf7d0;
}
</style>
</head>

<body>

<form class="register-box" method="POST" action="">
    <div class="title">Create Account</div>
    <div class="subtitle">Student Registration</div>

    <?php if ($message): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="department" placeholder="Department" required>
    <input type="password" name="password" placeholder="Password" required>

    <button class="btn" type="submit" name="register">Register</button>

    <div class="link">
        Already user? <a href="index.php">Login Now</a>
    </div>
</form>

</body>
</html>
