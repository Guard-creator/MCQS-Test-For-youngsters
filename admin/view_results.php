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

// ── Fetch All Test Results ────────────────────────────────────────
$results = $conn->query(
    "SELECT t.id AS test_id, u.username,
            t.total_question, t.correct_answer, t.wrong_answer, t.score, t.taken_at
     FROM tests t
     JOIN users u ON t.user_id = u.id
     ORDER BY t.taken_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Test Results</title>
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
            margin-bottom: 16px;
        }

        .btn {
            display: inline-block;
            padding: 9px 16px;
            background: #2563eb;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn:hover {
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
    </style>
</head>
<body>

<div class="container">

    <h2>All Test Results</h2>

    <a class="btn" href="index.php">&larr; Back to Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>Test ID</th>
                <th>User</th>
                <th>Total Qs</th>
                <th>Correct</th>
                <th>Wrong</th>
                <th>Score %</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['test_id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= $row['total_question'] ?></td>
                    <td><?= $row['correct_answer'] ?></td>
                    <td><?= $row['wrong_answer'] ?></td>
                    <td><?= $row['score'] ?>%</td>
                    <td><?= $row['taken_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>