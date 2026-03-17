<?php
// ── Session ───────────────────────────────────────────────────────
session_start();

// ── Admin Guard ───────────────────────────────────────────────────
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("DB Error: " . mysqli_connect_error());

// ── Validate User ID ──────────────────────────────────────────────
if (!isset($_GET['assign'])) {
    die("No user selected.");
}

$user_id = (int)$_GET['assign'];

// ── Fetch All MCQ IDs ─────────────────────────────────────────────
$result  = $conn->query("SELECT id FROM mcqs");
$mcq_ids = [];

while ($row = $result->fetch_assoc()) {
    $mcq_ids[] = $row['id'];
}

// ── Insert Assignments ────────────────────────────────────────────
$stmt = $conn->prepare("INSERT INTO test_assignments (user_id, mcq_id) VALUES (?, ?)");

foreach ($mcq_ids as $mid) {
    $stmt->bind_param("ii", $user_id, $mid);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Assigned</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #eef2ff;
            margin: 0;
            padding: 30px;
        }

        .box {
            max-width: 480px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

<div class="box">
    <div class="icon">✅</div>
    <h2>Test Successfully Assigned!</h2>
    <p>The test has been assigned to the selected user.</p>
    <a class="btn" href="./assign_test.php">&larr; Back to Assign Test</a>
</div>

</body>
</html>

