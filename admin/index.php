<?php
// ── Session ───────────────────────────────────────────────────────
session_start();

// ── Admin Guard ───────────────────────────────────────────────────
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("DB Error: " . mysqli_connect_error());

// ── Fetch All Users ───────────────────────────────────────────────
$users = $conn->query("SELECT id, username, email, role FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #eef2ff;
            padding: 30px;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            color: #1e3a8a;
        }

        .nav {
            display: flex;
            gap: 10px;
            margin: 14px 0;
            flex-wrap: wrap;
        }

        .nav a {
            text-decoration: none;
            color: #fff;
            background: #2563eb;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .nav a:hover {
            background: #1d4ed8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .nav a.btn-danger {
            background: #ef4444;
        }

        .nav a.btn-danger:hover {
            background: #dc2626;
        }

    </style>
</head>
<body>

<div class="container">

    <h2>Admin Dashboard</h2>

    <nav class="nav">
        <a href="manage_mcqs.php">Manage MCQs</a>
        <a href="assign_test.php">Assign Test to User</a>
        <a href="view_results.php">View Test Results</a>
        <a class="btn-danger" href="logout.php">Logout</a>
    </nav>

    <h3>Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>