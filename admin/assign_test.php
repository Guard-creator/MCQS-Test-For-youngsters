<?php
// ── Session ───────────────────────────────────────────────────────
session_start();

// ── Admin Guard ───────────────────────────────────────────────────
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ── DB Connection ─────────────────────────────────────────────────
$conn = mysqli_connect("localhost", "root", "", "login_system");
if (!$conn) die("DB Error: " . mysqli_connect_error());

// ── Handle Assignment ─────────────────────────────────────────────
if (isset($_POST['assign'])) {
    $user_id       = intval($_POST['user_id']);
    $num_questions = intval($_POST['num_questions']);

    $_SESSION['admin_assign'] = [
        'user_id'       => $user_id,
        'num_questions' => $num_questions
    ];

    header("Location: start_test.php?assign=1");
    exit;
}

// ── Fetch Users ───────────────────────────────────────────────────
$users = $conn->query("SELECT id, username FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Test</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #eef2ff;
            padding: 30px;
            margin: 0;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-bottom: 18px;
            color: #1e3a8a;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        select,
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        select:focus,
        input[type="number"]:focus {
            border-color: #2563eb;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 11px;
            border-radius: 8px;
            border: none;
            font-family: Poppins, sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            box-sizing: border-box;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
            margin-bottom: 10px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Assign Test to User</h2>

    <form method="POST">

        <label for="user_id">Select User</label>
        <select id="user_id" name="user_id" required>
            <option value="">-- Select a User --</option>
            <?php while ($u = $users->fetch_assoc()): ?>
                <option value="<?= $u['id'] ?>">
                    <?= htmlspecialchars($u['username']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="num_questions">Number of Questions</label>
        <input type="number" id="num_questions" name="num_questions"
               placeholder="e.g. 20" min="1" required>

        <button class="btn btn-primary" type="submit" name="assign">Assign Test</button>

    </form>

    <a class="btn btn-secondary" href="index.php">&larr; Back to Dashboard</a>

</div>

</body>
</html>