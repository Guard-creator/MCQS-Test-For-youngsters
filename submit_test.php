<?php
// ── Session & Cache ───────────────────────────────────────────────
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

// ── Auth Guard ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ── Validate Test Session ─────────────────────────────────────────
if (
    empty($_SESSION['test_mcqs']) ||
    empty($_SESSION['test_answers']) ||
    !is_array($_SESSION['test_mcqs'])
) {
    die("Test session expired or invalid. Please start again.");
}

$mcq_ids   = $_SESSION['test_mcqs'];
$submitted = $_SESSION['test_answers'];
$total_q   = count($mcq_ids);

// ── Fetch Correct Answers ─────────────────────────────────────────
$ids_string  = implode(",", array_map('intval', $mcq_ids));
$correct_map = [];

$res = $conn->query("SELECT id, correct_option FROM mcqs WHERE id IN ($ids_string)");
while ($row = $res->fetch_assoc()) {
    $correct_map[$row['id']] = $row['correct_option'];
}

// ── Grade Each Answer ─────────────────────────────────────────────
$correct          = 0;
$wrong            = 0;
$answers_to_insert = [];

foreach ($mcq_ids as $qid) {
    $selected   = $submitted[$qid] ?? null;
    $is_correct = ($selected !== null && $selected === $correct_map[$qid]) ? 1 : 0;

    if ($is_correct) $correct++;
    else             $wrong++;

    $answers_to_insert[] = [
        'mcq_id'     => $qid,
        'selected'   => $selected,
        'is_correct' => $is_correct
    ];
}

$score = round(($correct / $total_q) * 100, 2);

// ── Save Test Summary ─────────────────────────────────────────────
$insertTest = $conn->prepare(
    "INSERT INTO tests (user_id, total_question, correct_answer, wrong_answer, score)
     VALUES (?, ?, ?, ?, ?)"
);
$insertTest->bind_param("iiids", $user_id, $total_q, $correct, $wrong, $score);
$insertTest->execute();
$test_id = $insertTest->insert_id;

// ── Save Individual Answers ───────────────────────────────────────
$insertAns = $conn->prepare(
    "INSERT INTO user_answers (test_id, mcqs_id, selected_option, is_correct)
     VALUES (?, ?, ?, ?)"
);

foreach ($answers_to_insert as $a) {
    $sel = in_array($a['selected'], ['a', 'b', 'c', 'd']) ? $a['selected'] : null;
    $insertAns->bind_param("iisi", $test_id, $a['mcq_id'], $sel, $a['is_correct']);
    $insertAns->execute();
}

// ── Clear Test Session ────────────────────────────────────────────
unset($_SESSION['test_mcqs'], $_SESSION['test_answers'], $_SESSION['test_index']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Result</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 30px;
        }

        .box {
            max-width: 560px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .score {
            font-size: 40px;
            color: #10b981;
            margin: 12px 0;
        }

        .summary {
            color: #6b7280;
        }

        .actions {
            margin-top: 18px;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            background: #2563eb;
            margin: 8px;
        }

        .btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Test Completed</h2>

    <div class="score"><?= $score ?>%</div>
    <div class="summary"><?= $correct ?> correct out of <?= $total_q ?> &bull; <?= $wrong ?> wrong</div>

    <div class="actions">
        <a class="btn" href="dashboard.php">Back to Dashboard</a>
        <a class="btn" href="start_test.php">Take Another Test</a>
    </div>
</div>

</body>
</html>