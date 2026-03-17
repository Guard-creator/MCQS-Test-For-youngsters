<?php
// ── Session ───────────────────────────────────────────────────────
session_start();

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

// ── Auth Guard ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ── Validate Test ID ──────────────────────────────────────────────
$test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
if ($test_id <= 0) die("Invalid test ID.");

// ── Fetch Test Summary ────────────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT id, total_question, correct_answer, wrong_answer, score, taken_at
     FROM tests
     WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $test_id, $user_id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) die("Test not found or does not belong to you.");

// ── Fetch Question & Answer Details ──────────────────────────────
$stmt2 = $conn->prepare(
    "SELECT ua.mcqs_id, ua.selected_option, ua.is_correct,
            m.question, m.option_a, m.option_b, m.option_c, m.option_d, m.correct_option
     FROM user_answers ua
     JOIN mcqs m ON ua.mcqs_id = m.id
     WHERE ua.test_id = ?"
);
$stmt2->bind_param("i", $test_id);
$stmt2->execute();
$rows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Details</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #eef2ff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .meta {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .question-block {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #f8fafc;
        }

        .options {
            margin-top: 6px;
        }

        .your-answer {
            margin-top: 8px;
        }

        .correct {
            color: #10b981;
            font-weight: 700;
        }

        .wrong {
            color: #ef4444;
            font-weight: 700;
        }

        .back-link {
            display: inline-block;
            margin-top: 16px;
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Test #<?= $t['id'] ?> &mdash; Score: <?= $t['score'] ?>%</h2>

    <div class="meta">
        Taken at: <?= $t['taken_at'] ?> &bull;
        Correct: <?= $t['correct_answer'] ?> &bull;
        Wrong: <?= $t['wrong_answer'] ?>
    </div>

    <hr>

    <?php foreach ($rows as $i => $r): ?>
        <div class="question-block">

            <div><strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($r['question']) ?></div>

            <div class="options">
                <div <?= $r['correct_option'] === 'a' ? 'class="correct"' : '' ?>>A. <?= htmlspecialchars($r['option_a']) ?></div>
                <div <?= $r['correct_option'] === 'b' ? 'class="correct"' : '' ?>>B. <?= htmlspecialchars($r['option_b']) ?></div>
                <div <?= $r['correct_option'] === 'c' ? 'class="correct"' : '' ?>>C. <?= htmlspecialchars($r['option_c']) ?></div>
                <div <?= $r['correct_option'] === 'd' ? 'class="correct"' : '' ?>>D. <?= htmlspecialchars($r['option_d']) ?></div>
            </div>

            <div class="your-answer">
                Your answer:
                <?php if ($r['selected_option'] === null): ?>
                    <span class="wrong">No answer</span>
                <?php elseif ($r['is_correct']): ?>
                    <span class="correct"><?= strtoupper($r['selected_option']) ?> (Correct)</span>
                <?php else: ?>
                    <span class="wrong"><?= strtoupper($r['selected_option']) ?> (Wrong)</span>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>

    <a class="back-link" href="dashboard.php">&larr; Back to Dashboard</a>

</div>

</body>
</html>