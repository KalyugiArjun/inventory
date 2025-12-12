<?php
require_once __DIR__ . "/auth.php";
require_login();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($pageTitle) ? $pageTitle : "College Inventory System"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg: #020617;
            --bg-soft: #0b1120;
            --bg-card: #020617;
            --border: #1f2937;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #f97316;
            --accent-soft: #fb923c;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }

        body {
            background: radial-gradient(circle at top, #111827 0, #020617 45%, #000 100%);
            /* background-color: #fff; */
            color: var(--text);
            min-height: 100vh;
        }

        a { text-decoration: none; color: inherit; }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 230px;
            background: linear-gradient(180deg, #020617, #030712);
            border-right: 1px solid var(--border);
            padding: 18px 14px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: radial-gradient(circle, var(--accent-soft), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #111827;
            box-shadow: 0 0 15px rgba(249,115,22,0.45);
        }
        .brand-text { font-size: 16px; font-weight: 600; }

        .role-badge {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--muted);
            margin: 14px 4px 6px;
            letter-spacing: 0.06em;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 14px;
            color: var(--muted);
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.1s;
        }

        .nav-link span.icon {
            font-size: 16px;
        }

        .nav-link.active,
        .nav-link:hover {
            background: rgba(249,115,22,0.15);
            color: var(--accent-soft);
            transform: translateY(-1px);
        }

        .logout-btn {
            margin-top: 20px;
            padding: 8px 10px;
            width: 100%;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid rgba(248,250,252,0.08);
            text-align: center;
            cursor: pointer;
            background: transparent;
            color: var(--muted);
        }
        .logout-btn:hover {
            border-color: rgba(249,115,22,0.6);
            color: var(--accent-soft);
        }

        /* MAIN */
        .main {
            flex: 1;
            padding: 16px;
        }

        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 16px;
            gap: 10px;
        }

        .user-pill {
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.35);
            font-size: 12px;
        }

        .user-pill span {
            color: var(--accent-soft);
            font-weight: 500;
        }

        .content {
            max-width: 1120px;
            margin: 0 auto;
        }

        .card {
            background: rgba(15,23,42,0.9);
            border-radius: 18px;
            padding: 18px 16px;
            margin-bottom: 16px;
            border: 1px solid rgba(15,23,42,0.9);
            box-shadow: 0 18px 45px rgba(0,0,0,0.7);
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .btn {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 999px;
            border: none;
            font-size: 13px;
            cursor: pointer;
            background: var(--accent);
            color: #111827;
            font-weight: 500;
            box-shadow: 0 0 0 rgba(249,115,22,0);
            transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.15s;
        }

        .btn:hover {
            background: var(--accent-soft);
            box-shadow: 0 8px 24px rgba(249,115,22,0.4);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--accent-soft);
            border: 1px solid rgba(249,115,22,0.5);
        }

        .btn-outline:hover {
            background: rgba(249,115,22,0.1);
        }

        .grid {
            display: grid;
            gap: 12px;
        }

        @media (min-width: 768px) {
            .grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .stat {
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(30,64,175,0.7);
            background: radial-gradient(circle at top, rgba(30,64,175,0.35), rgba(15,23,42,0.9));
            font-size: 13px;
        }

        .stat-label { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
        .stat-value { font-size: 18px; font-weight: 600; }
        .stat-hint { font-size: 11px; color: var(--muted); margin-top: 2px; }

        .tag {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            border: 1px solid rgba(148,163,184,0.4);
            color: var(--muted);
        }

        /* RESPONSIVE: sidebar collapse on small screens */
        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 8px;
            }
            .brand {
                margin-bottom: 10px;
            }
            .nav-section-title {
                display: none;
            }
            .nav-links {
                display: flex;
                gap: 4px;
                flex-wrap: wrap;
            }
            .nav-link {
                font-size: 12px;
                padding: 6px 8px;
            }
            .logout-btn {
                width: auto;
                font-size: 12px;
                padding: 6px 10px;
            }
            .main {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">CI</div>
            <div>
                <div class="brand-text">College Inventory</div>
                <div class="role-badge">
                    <?php echo ucfirst($user['role']); ?>
                </div>
            </div>
        </div>

         <div class="nav-section-title">Main</div>
        <ul class="nav-links">
            <!-- Dashboard -->
            <li>
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false ? 'active' : ''); ?>"
                   href="<?php
                       if ($user['role'] === 'admin') echo '/admin/dashboard.php';
                       elseif ($user['role'] === 'storekeeper') echo '/store/dashboard.php';
                       elseif ($user['role'] === 'student') echo '/student/dashboard.php';
                       else echo '/faculty/dashboard.php';
                   ?>">
                    <span class="icon">🏠</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Admin + Storekeeper menus -->
            <?php if (in_array($user['role'], ['admin','storekeeper'])): ?>
                <li>
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'items') !== false ? 'active' : ''); ?>"
                       href="/admin/items.php">
                        <span class="icon">📦</span>
                        <span>Items</span>
                    </a>
                </li>

                <li>
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'issue_item') !== false ? 'active' : ''); ?>"
                       href="/store/issue_item.php">
                        <span class="icon">📤</span>
                        <span>Issue Items</span>
                    </a>
                </li>

                <li>
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'issue_records') !== false ? 'active' : ''); ?>"
                       href="/store/issue_records.php">
                        <span class="icon">📚</span>
                        <span>Issue Records</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Student + Faculty: My Items -->
            <?php if (in_array($user['role'], ['student','faculty'])): ?>
                <li>
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'my_items') !== false ? 'active' : ''); ?>"
                       href="<?php
                           if ($user['role'] === 'student') echo '/student/my_items.php';
                           else echo '/faculty/my_items.php';
                       ?>">
                        <span class="icon">📦</span>
                        <span>My Items</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>


        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="user-pill">
                Logged in as <span><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
        </div>
        <div class="content">
