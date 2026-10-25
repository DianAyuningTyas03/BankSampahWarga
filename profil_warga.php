<?php
session_start();
require_once __DIR__ . '/api/koneksi.php';

$username = trim($_GET['username'] ?? '');

if ($username === '' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

if ($username === '') {
    die("Username tidak ditemukan. Silakan login kembali melalui aplikasi.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, nama, username, role
     FROM users
     WHERE username = ? AND role = 'warga'
     LIMIT 1"
);

if (!$stmt) {
    die("Terjadi kesalahan pada database.");
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$warga = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$warga) {
    die("Data profil warga tidak ditemukan.");
}

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Warga</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            color: #1f2937;
        }

        .container {
            max-width: 450px;
            margin: 30px auto;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: 35px;
        }

        h2 {
            margin: 0 0 8px;
            color: #166534;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .item {
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            display: block;
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #dcfce7;
            color: #166534;
            border-radius: 20px;
            font-size: 13px;
        }

        .back {
            display: block;
            margin-top: 22px;
            padding: 13px;
            text-align: center;
            text-decoration: none;
            background: #15803d;
            color: white;
            border-radius: 10px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="card">
        <div class="header">
            <div class="avatar">👤</div>
            <h2>Profil Warga</h2>
            <div class="subtitle">Informasi akun Bank Sampah</div>
        </div>

        <div class="item">
            <span class="label">Nama Lengkap</span>
            <div class="value"><?= h($warga['nama']) ?></div>
        </div>

        <div class="item">
            <span class="label">Username</span>
            <div class="value"><?= h($warga['username']) ?></div>
        </div>

        <div class="item">
            <span class="label">Role</span>
            <span class="badge"><?= h($warga['role']) ?></span>
        </div>

        <a class="back" href="javascript:history.back()">Kembali</a>
    </div>
</div>
</body>
</html>