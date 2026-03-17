<?php
// ── Session & DB Setup ────────────────────────────────────────────
session_start();

$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("DB Failed: " . mysqli_connect_error());

// ── Auth Guard ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────
$username      = htmlspecialchars($_SESSION['user'] ?? 'User');
$num_questions = isset($_SESSION['admin_assign'])
                    ? intval($_SESSION['admin_assign']['num_questions'])
                    : 20;

// ── Handle Start Test ─────────────────────────────────────────────
if (isset($_POST['start_test'])) {

    $stmt = $conn->prepare("SELECT id FROM mcqs ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("i", $num_questions);
    $stmt->execute();
    $result = $stmt->get_result();

    $mcqs = [];
    while ($row = $result->fetch_assoc()) {
        $mcqs[] = $row['id'];
    }

    $_SESSION['test_mcqs']     = $mcqs;
    $_SESSION['test_index']    = 0;
    $_SESSION['test_answers']  = [];

    header("Location: take_question.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Test</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #f1f5f9;
            margin: 0;
        }

        .box {
            width: 420px;
            max-width: 95%;
            margin: 80px auto;
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            background: #2563eb;
            display: inline-block;
            margin-top: 12px;
            border: none;
            cursor: pointer;
            font-family: Poppins, sans-serif;
            font-size: 15px;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .back-link {
            display: block;
            margin-top: 14px;
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Welcome, <?= $username ?>!</h2>

    <p>You will answer <strong><?= $num_questions ?></strong> multiple-choice questions.</p>

    <form method="POST">
        <button class="btn" name="start_test" type="submit">Start Test</button>
    </form>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>