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

// ── Handle Add Question ───────────────────────────────────────────
if (isset($_POST['add'])) {
    $stmt = $conn->prepare(
        "INSERT INTO mcqs (question, option_a, option_b, option_c, option_d, correct_option)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssss",
        $_POST['question'],
        $_POST['option_a'],
        $_POST['option_b'],
        $_POST['option_c'],
        $_POST['option_d'],
        $_POST['correct_option']
    );
    $stmt->execute();
}

// ── Handle Delete Question ────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id   = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM mcqs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// ── Fetch All MCQs ────────────────────────────────────────────────
$mcqs = $conn->query("SELECT * FROM mcqs ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage MCQs</title>
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

        h3 {
            color: #374151;
            margin-top: 24px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 4px;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 9px 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
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

        .btn-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th, td {
            padding: 10px 12px;
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

        .delete-link {
            color: #ef4444;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .delete-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Manage MCQs</h2>

    <h3>Add New Question</h3>
    <form method="POST">

        <label for="question">Question</label>
        <textarea id="question" name="question" placeholder="Enter question text..." required></textarea>

        <label for="option_a">Option A</label>
        <input type="text" id="option_a" name="option_a" placeholder="Option A" required>

        <label for="option_b">Option B</label>
        <input type="text" id="option_b" name="option_b" placeholder="Option B" required>

        <label for="option_c">Option C</label>
        <input type="text" id="option_c" name="option_c" placeholder="Option C" required>

        <label for="option_d">Option D</label>
        <input type="text" id="option_d" name="option_d" placeholder="Option D" required>

        <label for="correct_option">Correct Option</label>
        <select id="correct_option" name="correct_option" required>
            <option value="a">A</option>
            <option value="b">B</option>
            <option value="c">C</option>
            <option value="d">D</option>
        </select>

        <div class="btn-actions">
            <button class="btn btn-primary" type="submit" name="add">Add Question</button>
            <a class="btn btn-secondary" href="index.php">&larr; Back to Dashboard</a>
        </div>

    </form>

    <h3>Existing Questions</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Correct Option</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($m = $mcqs->fetch_assoc()): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td><?= htmlspecialchars($m['question']) ?></td>
                    <td><?= strtoupper($m['correct_option']) ?></td>
                    <td><a class="delete-link" href="?delete=<?= $m['id'] ?>">Delete</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>