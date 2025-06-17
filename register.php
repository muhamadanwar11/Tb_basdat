<?php
include 'config.php';

// Fungsi aman untuk membersihkan input
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $jenis_kelamin = sanitize($_POST['jenis_kelamin']);
    $tanggal_lahir = sanitize($_POST['tanggal_lahir']);
    $username = sanitize($_POST['username']);
    $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);
    $alamat = sanitize($_POST['alamat']);
    $no_telepon = sanitize($_POST['no_telepon']);

    // Cek apakah username sudah digunakan
    $check_sql = "SELECT id_nasabah FROM nasabah WHERE username = :username";
    $stmt = $conn->prepare($check_sql);
    $stmt->execute(['username' => $username]);

    if ($stmt->rowCount() > 0) {
        $error = "Username sudah digunakan.";
    } else {
        $sql = "INSERT INTO nasabah (nama, jenis_kelamin, tanggal_lahir, username, password, alamat, no_telepon) 
                VALUES (:nama, :jk, :tgl, :username, :password, :alamat, :no_telp)";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            'nama' => $nama,
            'jk' => $jenis_kelamin,
            'tgl' => $tanggal_lahir,
            'username' => $username,
            'password' => $password,
            'alamat' => $alamat,
            'no_telp' => $no_telepon
        ]);

        if ($success) {
            $success = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data.";
        }
    }
}
?>

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nasabah - Perbankan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Daftar Nasabah Baru</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
            <div class="login-link">
                <a href="login.php">Kembali ke Login</a>
            </div>
        <?php else: ?>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat">
                </div>
                
                <div class="form-group">
                    <label for="no_telepon">No. Telepon</label>
                    <input type="text" id="no_telepon" name="no_telepon">
                </div>
                
                <button type="submit">Daftar</button>
                
                <div class="login-link">
                    Sudah punya akun? <a href="login.php">Login disini</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>