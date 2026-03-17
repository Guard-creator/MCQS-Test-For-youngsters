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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {


        
            $_SESSION['user'] = $row['username'];
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['user_role'] = $row['role'];

            if ($_SESSION['user_role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
  
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    width: 100%;
    max-width: 400px;
    background: #fff;
    padding: 40px 36px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);
}

.logo {
    display: flex;
    justify-content: center;
    margin-bottom: 8px;
}

.logo svg {
    width: 44px;
    height: 44px;
}

h2 {
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
}

.subtitle {
    text-align: center;
    font-size: 13px;
    color: #94a3b8;
    margin-bottom: 28px;
}

.field {
    margin-bottom: 18px;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 11px 14px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    font-family: inherit;
    font-size: 14px;
    color: #1e293b;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}

input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
}

.forgot {
    display: block;
    text-align: right;
    font-size: 12px;
    color: #2563eb;
    text-decoration: none;
    margin-top: -10px;
    margin-bottom: 22px;
}

.forgot:hover {
    color: #1e40af;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: #fff;
    font-family: inherit;
    font-size: 15px;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}

button[type="submit"]:hover {
    background: #1e40af;
}

button[type="submit"]:active {
    transform: scale(0.99);
}

.register-link {
    text-align: center;
    margin-top: 22px;
    font-size: 13px;
    color: #64748b;
}

.register-link a {
    color: #2563eb;
    font-weight: 500;
    text-decoration: none;
}

.register-link a:hover {
    color: #1e40af;
}

.error {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fef2f2;
    padding: 12px 14px;
    border-left: 4px solid #dc2626;
    border-radius: 8px;
    margin-bottom: 20px;
    color: #b91c1c;
    font-size: 13.5px;
    line-height: 1.4;
}

.error svg {
    flex-shrink: 0;
    margin-top: 1px;
}
</style>
</head>
<body>

<div class="container">

    <div class="logo">
        <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="44" height="44" rx="12" fill="#89a7ff"/>
            <path d="M14 22C14 17.582 17.582 14 22 14C26.418 14 30 17.582 30 22C30 26.418 26.418 30 22 30C17.582 30 14 26.418 14 22Z" stroke="#2563eb" stroke-width="2"/>
            <path d="M19 22L21.5 24.5L25.5 19.5" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your account to continue</p>

    <?php if (!empty($error)): ?>
        <div class="error" role="alert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 5v3M8 10.5v.5M2 8a6 6 0 1 0 12 0A6 6 0 0 0 2 8z"
                      stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com"
                   autocomplete="email" required>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••"
                   autocomplete="current-password" required>
        </div>

        <a href="forgot.php" class="forgot">Forgot password?</a>

        <button type="submit">Sign in</button>
    </form>

    <p class="register-link">
        Don't have an account? <a href="index.php">Create one</a>
    </p>

</div>

</body>
</html>