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

    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob      = $_POST['dob'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($check) > 0) {

        $error = "Email already exists!";

    } else {

        $image = "";
        if (!empty($_FILES['image']['name'])) {
            $image = time() . "_" . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }

        $sql = "INSERT INTO users (username, email, password, dob, image) 
                VALUES ('$username', '$email', '$password', '$dob', '$image')";

        if (mysqli_query($conn, $sql)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Failed to Register!";
        }

    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #dbeafe, #e0e7ff);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    width: 380px;
    background: #ffffff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1e3a8a;
    font-size: 26px;
    font-weight: 600;
}

input[type="text"], 
input[type="email"], 
input[type="password"], 
input[type="date"], 
input[type="file"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 16px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 15px;
    background: #f8fafc;
    transition: 0.2s;
}

input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 4px rgba(37, 99, 235, 0.4);
    outline: none;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    cursor: pointer;
    margin-top: 10px;
    font-weight: 600;
    transition: 0.25s;
}

button:hover {
    background: #1e40af;
    transform: translateY(-1px);
}

.error {
    background: #fee2e2;
    padding: 12px;
    border-left: 5px solid #dc2626;
    margin-bottom: 18px;
    border-radius: 8px;
    color: #b91c1c;
    font-size: 14px;
}

a {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #2563eb;
    font-weight: 500;
    text-decoration: none;
    transition: 0.2s;
}

a:hover {
    color: #1e40af;
}

</style>

</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="date" name="dob" required>
        <input type="file" name="image">

        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already have a Account?</a>
</div>

</body>
</html>
