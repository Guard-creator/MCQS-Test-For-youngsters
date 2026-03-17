<?php
// ── Session & Cache ───────────────────────────────────────────────
session_start();

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("DB Failed: " . mysqli_connect_error());

// ── Auth Guard ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ── Validate Test Session ─────────────────────────────────────────
if (!isset($_SESSION['test_mcqs'])) {
    die("No test started.");
}

$index = $_SESSION['test_index'];
$mcqs  = $_SESSION['test_mcqs'];

// ── Redirect if Test is Complete ──────────────────────────────────
if ($index >= count($mcqs)) {
    header("Location: submit_test.php");
    exit;
}

// ── Fetch Current Question ────────────────────────────────────────
$current_id = $mcqs[$index];

$stmt = $conn->prepare("SELECT * FROM mcqs WHERE id = ?");
$stmt->bind_param("i", $current_id);
$stmt->execute();
$q = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCQ Question</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2ff, #ffffff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .box {
            background: #fff;
            max-width: 750px;
            width: 100%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .question {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #1e3a8a;
        }

        .opt {
            display: block;
            margin: 12px 0;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
        }

        .opt:hover {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .opt input[type="radio"] {
            margin-right: 12px;
            accent-color: #2563eb;
        }

        .btn {
            display: inline-block;
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 15px 0;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }

        @media (max-width: 500px) {
            .box  { padding: 20px; }
            .question { font-size: 20px; }
            .btn  { font-size: 16px; }
        }
    </style>
</head>
<body>

<div class="box">
    <div class="question">Q<?= $index + 1 ?>. <?= htmlspecialchars($q['question']) ?></div>

    <form method="POST" action="save_answer.php">
        <label class="opt"><input type="radio" name="answer" value="a" required> <?= htmlspecialchars($q['option_a']) ?></label>
        <label class="opt"><input type="radio" name="answer" value="b" required> <?= htmlspecialchars($q['option_b']) ?></label>
        <label class="opt"><input type="radio" name="answer" value="c" required> <?= htmlspecialchars($q['option_c']) ?></label>
        <label class="opt"><input type="radio" name="answer" value="d" required> <?= htmlspecialchars($q['option_d']) ?></label>

        <button class="btn" type="submit">Next Question &rarr;</button>
    </form>
</div>

</body>
</html>