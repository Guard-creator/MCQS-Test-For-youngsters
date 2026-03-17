<?php

session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "login_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user'] ?? 'User';

$sql = "SELECT COUNT(*) AS total_tests, COALESCE(SUM(correct_answer),0) AS total_correct, COALESCE(SUM(wrong_answer),0) AS total_wrong
        FROM tests WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$total_tests = $res['total_tests'] ?? 0;
$total_correct = $res['total_correct'] ?? 0;
$total_wrong = $res['total_wrong'] ?? 0;

$sql2 = "SELECT id, total_question, correct_answer, wrong_answer, score, taken_at FROM tests WHERE user_id = ? ORDER BY taken_at DESC LIMIT 10";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result_tests = $stmt2->get_result();
?>
    <!DOCTYPE html>
    <html>
    <head>
    <title>Dashboard - MCQ Test</title>
    <style>

    body { font-family: Poppins, sans-serif; background: linear-gradient(135deg,#6366f1,#a78bfa); margin:0; padding:0;}
    .container { width: 900px; max-width:95%; margin: 40px auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.15);}
    .header { display:flex; justify-content:space-between; align-items:center;}
    h2 { color:#4f46e5; margin:0;}
    .stats { display:flex; gap:20px; margin:20px 0;}
    .card { background:#f8fafc; padding:15px; border-radius:8px; min-width:150px; text-align:center; }
    .btn { padding:10px 16px; border-radius:8px; text-decoration:none; color:#fff; }
    .btn-start { background:#10b981; }
    .btn-logout { background:#ef4444; }
    .table { width:100%; border-collapse: collapse; margin-top:15px; }
    .table th, .table td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
    .small { font-size:13px; color:#6b7280; }
    </style>
    </head>
    <body>
    <div class="container">
      <div class="header">
        <h2>Welcome, <?=htmlspecialchars($username)?> 👋</h2>
        <div>
          <a class="btn btn-start" href="start_test.php">Start Test</a>
          <a class="btn btn-logout" href="logout.php">Logout</a>
        </div>
      </div>

      <div class="stats">
        <div class="card">
          <div class="small">Tests Taken</div>
          <div><strong><?= $total_tests ?></strong></div>
        </div>
        <div class="card">
          <div class="small">Total Correct</div>
          <div><strong><?= $total_correct ?></strong></div>
        </div>
        <div class="card">
          <div class="small">Total Wrong</div>
          <div><strong><?= $total_wrong ?></strong></div>
        </div>
      </div>

      <h3>Recent Attempts</h3>
      <table class="table">
        <thead>
          <tr><th>#</th><th>Date</th><th>Correct</th><th>Wrong</th><th>Score (%)</th><th>View</th></tr>
        </thead>
        <tbody>
        <?php if ($result_tests->num_rows === 0): ?>
          <tr><td colspan="6">No tests yet — try starting one!</td></tr>
        <?php else: ?>
          <?php while ($row = $result_tests->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['taken_at']) ?></td>
              <td><?= htmlspecialchars($row['correct_answer']) ?></td>
              <td><?= htmlspecialchars($row['wrong_answer']) ?></td>
              <td><?= htmlspecialchars($row['score']) ?></td>
              <td><a href="view_test.php?test_id=<?= $row['id'] ?>">Details</a></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    </body>
    </html>
