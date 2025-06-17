<?php
include 'config.php';
session_start();

$error = '';

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    // 1. Cek di tabel customer_service (admin/cs)
    $stmt = $conn->prepare("SELECT * FROM customer_service WHERE username = ?");
    $stmt->execute([$username]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['user_id'] = $staff['id_cs'];
        $_SESSION['nama'] = $staff['nama'];
        $_SESSION['user_type'] = $staff['role']; // 'admin' atau 'cs'
        header("Location: index.php");
        exit;
    }

    // 2. Cek di tabel nasabah
    $stmt = $conn->prepare("SELECT * FROM nasabah WHERE username = ?");
    $stmt->execute([$username]);
    $nasabah = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nasabah && password_verify($password, $nasabah['password'])) {
        $_SESSION['user_id'] = $nasabah['id_nasabah'];
        $_SESSION['nama'] = $nasabah['nama'];
        $_SESSION['user_type'] = 'nasabah';
        header("Location: index.php");
        exit;
    }

    $error = "Username atau password salah.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Perbankan</title>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 360px;
        }
        h2 {
            text-align: center;
            color: #0146a6;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 0.7rem;
            background-color: #0146a6;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }
        button:hover {
            background-color: #013b90;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
        .link {
            text-align: center;
            margin-top: 1rem;
            color: #555;
        }
        .link a {
            color: #0146a6;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Perbankan</h2>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Masuk</button>

            <div class="link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </form>
    </div>
</body>
</html>