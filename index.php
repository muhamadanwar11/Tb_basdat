<?php
include 'config.php';
session_start();

$user_type = $_SESSION['user_type'] ?? null;
$nama = $_SESSION['nama'] ?? 'Pengunjung';

// Ambil data statistik
$totalNasabah = $conn->query("SELECT COUNT(*) FROM nasabah")->fetchColumn();
$totalRekening = $conn->query("SELECT COUNT(*) FROM rekening")->fetchColumn();
$totalBank = $conn->query("SELECT COUNT(*) FROM bank")->fetchColumn();
$totalTransfer = $conn->query("SELECT COUNT(*) FROM transfer")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome - Perbankan</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background-color: #f4f6f9;
    }
    .sidebar {
      width: 220px;
      background-color: #0146a6;
      color: white;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      margin: 10px 0;
      display: block;
      padding: 10px;
      border-radius: 5px;
    }
    .sidebar a:hover {
      background-color: #0056b3;
    }
    .main-content {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }
    .header {
      background-color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .logout-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      text-decoration: none;
    }
    .logout-btn:hover {
      background-color: #c82333;
    }
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
    }
    .stat-card .label {
      font-size: 14px;
      color: #666;
      margin-bottom: 10px;
    }
    .stat-card .value {
      font-size: 24px;
      font-weight: bold;
      color: #0146a6;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #0146a6;
      color: white;
    }
    tr:hover {
      background-color: #f5f5f5;
    }
  </style>
</head>
<body>

<div class="sidebar">
    <h2>Perbankan</h2>
    <a href="index.php">🏠 Beranda</a>
    
    <?php if ($user_type == 'admin'): ?>
        <!-- Menu Admin (tetap seperti sebelumnya) -->
        <a href="user/admin/nasabah.php">👥 Nasabah</a>
        <a href="user/admin/cs.php">👨‍💼 Customer Service</a>
        <a href="user/admin/rekening.php">💳 Rekening</a>
        <a href="user/admin/bank.php">🏦 Bank</a>
        <a href="user/admin/transfer.php">💰 Transfer</a>

    <?php elseif ($user_type == 'cs'): ?>
        <!-- Menu Khusus CS -->
        <a href="user/cs/nasabah.php">👥 Nasabah</a>
        <a href="user/cs/rekening.php">💳 Rekening</a>
        <a href="user/cs/transfer.php">💰 Transfer</a>
        <a href="user/cs/keluhan.php">📝 layanan</a>
        <a href="user/cs/laporan.php">📊 Laporan</a>

    <?php elseif ($user_type == 'nasabah'): ?>
        <!-- Menu Nasabah (tetap seperti sebelumnya) -->
        <a href="user/nasabah/rekening.php">💳 Rekening Saya</a>
        <a href="user/nasabah/transfer.php">💰 Transfer</a>
        <a href="user/nasabah/topup.php">💸 Top Up</a>
    <?php else: ?>
        <a href="login.php">🔐 Login</a>
    <?php endif; ?>

    <?php if ($user_type): ?>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    <?php endif; ?>
</div>

<div class="main-content">
  <div class="header">
    <h1>Selamat datang, <?= htmlspecialchars($nama) ?>!</h1>
    <span>
      <?= $user_type ? 'Login sebagai: <strong>' . strtoupper($user_type) . '</strong>' : 'Anda belum login.' ?>
    </span>
  </div>

  <div class="stats-container">
    <div class="stat-card">
      <div class="label">Total Nasabah</div>
      <div class="value"><?= $totalNasabah ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Total Rekening</div>
      <div class="value"><?= $totalRekening ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Total Bank</div>
      <div class="value"><?= $totalBank ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Total Transfer</div>
      <div class="value"><?= $totalTransfer ?></div>
    </div>
  </div>

  <?php if ($user_type == 'nasabah' && isset($_SESSION['user_id'])): ?>
    <h2>Rekening Saya</h2>
    <?php
    $id_nasabah = $_SESSION['user_id'];
    $sql = "SELECT r.no_rekening, b.nama_bank, r.saldo 
            FROM rekening r 
            JOIN bank b ON r.id_bank = b.id_bank 
            WHERE r.id_nasabah = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_nasabah]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo "<table>";
        echo "<tr><th>No Rekening</th><th>Bank</th><th>Saldo</th></tr>";
        foreach ($result as $row) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['no_rekening']) . "</td>";
          echo "<td>" . htmlspecialchars($row['nama_bank']) . "</td>";
          echo "<td>Rp " . number_format($row['saldo'], 0, ',', '.') . "</td>";
          echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Anda belum memiliki rekening.</p>";
    }
    ?>
  <?php elseif ($user_type == 'admin' || $user_type == 'cs'): ?>
    <h2>Transfer Terakhir</h2>
    <?php
    $sql = "SELECT t.no_transfer, t.tanggal, t.nominal, 
                   b1.nama_bank as bank_pengirim, r1.no_rekening as rek_pengirim,
                   b2.nama_bank as bank_penerima, r2.no_rekening as rek_penerima,
                   t.status
            FROM transfer t
            JOIN rekening r1 ON t.no_rekening_pengirim = r1.no_rekening
            JOIN bank b1 ON r1.id_bank = b1.id_bank
            JOIN rekening r2 ON t.no_rekening_penerima = r2.no_rekening
            JOIN bank b2 ON r2.id_bank = b2.id_bank
            ORDER BY t.tanggal DESC LIMIT 5";
    $stmt = $conn->query($sql);
    $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transfers) {
        echo "<table>";
        echo "<tr>
                <th>No Transfer</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Nominal</th>
                <th>Status</th>
              </tr>";
        foreach ($transfers as $transfer) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($transfer['no_transfer']) . "</td>";
          echo "<td>" . htmlspecialchars($transfer['tanggal']) . "</td>";
          echo "<td>" . htmlspecialchars($transfer['bank_pengirim']) . " - " . htmlspecialchars($transfer['rek_pengirim']) . "</td>";
          echo "<td>" . htmlspecialchars($transfer['bank_penerima']) . " - " . htmlspecialchars($transfer['rek_penerima']) . "</td>";
          echo "<td>Rp " . number_format($transfer['nominal'], 0, ',', '.') . "</td>";
          echo "<td>" . htmlspecialchars($transfer['status']) . "</td>";
          echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Belum ada data transfer.</p>";
    }
    ?>
  <?php endif; ?>
</div>

</body>
</html>