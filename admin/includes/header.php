<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get the current page name to highlight the nav
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextGen Homes | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0F172A;
            --primary-light: #1E293B;
            --secondary: #059669;
            --accent: #D97706;
            --bg-gray: #F1F5F9;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-gray);
            color: #334155;
            display: flex;
            min-height: 100vh;
        }

        h1,
        h2,
        h3 {
            color: var(--primary);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary);
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--primary-light);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .sidebar-header span {
            color: var(--secondary);
        }

        .sidebar-header small {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: #94A3B8;
            margin-top: 0.25rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex-grow: 1;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #CBD5E1;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: var(--primary-light);
            color: white;
        }

        .sidebar-nav a.active {
            border-left-color: var(--secondary);
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--primary-light);
        }

        .sidebar-footer a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .sidebar-footer a:hover {
            color: white;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .topbar a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
        }

        .content-area {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Utilities */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #E2E8F0;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-size: 0.875rem;
            transition: 0.2s;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-primary:hover {
            background: #047857;
        }

        .btn-danger {
            background: #EF4444;
            color: white;
        }

        .btn-danger:hover {
            background: #DC2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
        }

        th {
            background-color: #F8FAFC;
            color: var(--primary);
            font-weight: 600;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-green {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-blue {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-yellow {
            background: #FEF3C7;
            color: #92400E;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            NextGen<span>Homes</span>
            <small>Dealership Admin Portal</small>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="inventory.php"
                class="<?php echo $current_page == 'inventory.php' || $current_page == 'inventory_edit.php' ? 'active' : ''; ?>">Manage
                Inventory</a>
            <a href="leads.php" class="<?php echo $current_page == 'leads.php' ? 'active' : ''; ?>">Requests & Leads</a>
            <a href="/" target="_blank" style="margin-top: 2rem;">↗ View Live Site</a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php">🚪 Log Out</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <span>Welcome, Admin</span>
        </div>

        <div class="content-area">