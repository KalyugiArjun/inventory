<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$error = "";

// Agar already login hai to direct redirect
if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location:admin/dashboard.php");
    } elseif ($_SESSION['role'] == 'storekeeper') {
        header("Location:store/dashboard.php");
    } elseif ($_SESSION['role'] == 'student') {
        header("Location:student/dashboard.php");
    } else {
        header("Location:faculty/dashboard.php");
    }
    exit;
}

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Important: user id ko $_SESSION['user'] me store kar rahe hain
        $_SESSION['user'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // Role ke hisaab se redirect
        if ($user['role'] == 'admin') {
            header("Location:admin/dashboard.php");
        } elseif ($user['role'] == 'storekeeper') {
            header("Location:store/dashboard.php"); // FIXED PATH
        } elseif ($user['role'] == 'student') {
            header("Location:student/dashboard.php");
        } else {
            header("Location:faculty/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - College Inventory</title>
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            background: radial-gradient(circle at top, #111827 0, #020617 45%, #000 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .wrapper {
            width: 100%;
            max-width: 880px;
            display: grid;
            gap: 18px;
        }

        @media (min-width: 768px) {
            .wrapper {
                grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
                align-items: center;
            }
        }

        .left-panel {
            display: none;
        }

        @media (min-width: 768px) {
            .left-panel {
                display: block;
                padding-right: 10px;
            }
        }

        .left-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .left-highlight {
            color: var(--accent-soft);
        }

        .left-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 18px;
            max-width: 380px;
        }

        .left-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .badge {
            padding: 4px 9px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.45);
            font-size: 11px;
            color: var(--muted);
            backdrop-filter: blur(12px);
            background: rgba(15,23,42,0.65);
        }

        .badge strong {
            color: var(--accent-soft);
            font-weight: 600;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            background: radial-gradient(circle at top left, rgba(251,146,60,0.16), rgba(15,23,42,0.98));
            border-radius: 20px;
            padding: 22px 18px 18px;
            border: 1px solid rgba(148,163,184,0.45);
            box-shadow: 0 22px 60px rgba(0,0,0,0.9);
            animation: popIn 0.35s ease-out;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            inset: -80px;
            opacity: 0.05;
            background:
                radial-gradient(circle at 0% 0%, rgba(249,115,22,0.9), transparent 55%),
                radial-gradient(circle at 100% 100%, rgba(249,115,22,0.6), transparent 55%);
            pointer-events: none;
        }

        .login-inner {
            position: relative;
            z-index: 1;
        }

        .login-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .logo-circle {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: radial-gradient(circle, var(--accent-soft), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #111827;
            box-shadow: 0 0 18px rgba(249,115,22,0.7);
        }

        .title-text {
            font-size: 17px;
            font-weight: 600;
        }

        .subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        label {
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
            color: var(--muted);
        }

        input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 11px;
            border: 1px solid var(--border);
            background: rgba(2,6,23,0.9);
            color: var(--text);
            font-size: 13px;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-soft);
            box-shadow: 0 0 0 1px rgba(249,115,22,0.45);
        }

        .password-wrap {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            border: none;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            padding: 2px 4px;
        }

        .toggle-password:hover {
            color: var(--accent-soft);
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            margin-top: 4px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--muted);
        }

        .remember input {
            width: auto;
            height: auto;
            accent-color: var(--accent);
        }

        .forgot {
            font-size: 11px;
            color: var(--muted);
            cursor: default;
        }

        .forgot span {
            color: var(--accent-soft);
        }

        .btn {
            margin-top: 8px;
            width: 100%;
            padding: 9px;
            border-radius: 999px;
            border: none;
            background: var(--accent);
            color: #111827;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 0 0 rgba(249,115,22,0);
            transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.15s;
        }

        .btn:hover {
            background: var(--accent-soft);
            box-shadow: 0 10px 28px rgba(249,115,22,0.5);
            transform: translateY(-1px);
        }

        .error {
            background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.5);
            color: #fecaca;
            font-size: 12px;
            border-radius: 10px;
            padding: 6px 8px;
            margin-bottom: 8px;
            animation: shake 0.25s;
        }

        .hint {
            margin-top: 10px;
            font-size: 11px;
            color: var(--muted);
            line-height: 1.4;
        }

        .hint code {
            background: rgba(15,23,42,0.9);
            padding: 2px 5px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.35);
            font-size: 10px;
        }

        @keyframes popIn {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-3px); }
            50% { transform: translateX(3px); }
            75% { transform: translateX(-2px); }
            100% { transform: translateX(0); }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Left Info Panel (Desktop only) -->
    <div class="left-panel">
        <div class="left-title">
            College <span class="left-highlight">Inventory</span> Portal
        </div>
        <div class="left-subtitle">
            Secure web-based system to manage <b>lab equipment, books, stationery and assets</b> 
            with role-based logins for Admin, Storekeeper, Students and Faculty.
        </div>
        <div class="left-badges">
            <div class="badge"><strong>Admin:</strong> Full control of users & inventory</div>
            <div class="badge"><strong>Storekeeper:</strong> Issue & return management</div>
            <div class="badge"><strong>Student:</strong> View issued items</div>
            <div class="badge"><strong>Faculty:</strong> Track assigned assets</div>
        </div>
    </div>

    <!-- Right Login Card -->
    <div class="login-card">
        <div class="login-inner">
            <div class="login-title">
                <div class="logo-circle">CI</div>
                <div>
                    <div class="title-text">College Inventory Login</div>
                    <div class="subtitle">Login with your role-based college credentials</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email ID</label>
                    <input type="email" name="email" required placeholder="e.g. admin@college.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrap">
                        <input type="password" id="passwordField" name="password" required placeholder="Enter password">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            Show
                        </button>
                    </div>
                </div>

                <div class="top-row">
                    <label class="remember">
                        <input type="checkbox" disabled>
                        Remember me (browser)
                    </label>
                    <div class="forgot">
                        Forgot password? <a href="register.php"><span>create account</span></a>
                    </div>
                </div>

                <button type="submit" name="login" class="btn">Login</button>
            </form>

           
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const field = document.getElementById('passwordField');
        const btn = document.querySelector('.toggle-password');
        if (!field) return;

        if (field.type === 'password') {
            field.type = 'text';
            btn.textContent = 'Hide';
        } else {
            field.type = 'password';
            btn.textContent = 'Show';
        }
    }
</script>
</body>
</html>
