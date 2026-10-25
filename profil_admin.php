<?php
session_start();
require_once __DIR__ . '/api/koneksi.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

$user_id = (int) $_SESSION['user_id'];

// Pastikan akun yang login adalah warga
$stmt = $conn->prepare(
    "SELECT id, nama FROM users WHERE id = ? AND role = 'warga' LIMIT 1"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$warga = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$warga) {
    die("Data warga tidak ditemukan. Periksa session login.");
}

// Ambil riwayat setoran warga
$stmt = $conn->prepare("
    SELECT
        s.tanggal,
        j.nama_sampah,
        s.berat,
        s.harga_per_kg,
        s.total_harga,
        s.status
    FROM setoran s
    LEFT JOIN jenis_sampah j ON j.id = s.jenis_sampah_id
    WHERE s.warga_id = ?
    ORDER BY s.tanggal DESC, s.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Setoran Warga</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            color: #1f2937;
        }

        header {
            background: #15803d;
            color: white;
            padding: 22px 16px;
        }

        header h1 {
            margin: 0 0 8px;
            font-size: 23px;
        }

        header p {
            margin: 0;
            font-size: 14px;
        }

        main {
            width: 100%;
            max-width: 1000px;
            margin: 24px auto;
            padding: 0 15px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
            margin-bottom: 18px;
        }

        .card h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            background: #dcfce7;
            color: #166534;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
        }

        .empty {
            padding: 25px 10px;
            text-align: center;
            color: #6b7280;
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            background: #15803d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
        }

        .button:hover {
            background: #166534;
        }

        .back {
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 20px;
            }

            .card {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Riwayat Setoran</h1>
    <p>Halo, <?= htmlspecialchars($warga['nama']) ?>!</p>
</header>

<main>
    <div class="back">
        <a class="button" href="javascript:history.back()">← Kembali</a>
    </div>

    <section class="card">
        <h2>Riwayat Sampah Saya</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Harga/kg</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?= htmlspecialchars($row['tanggal'] ?? '-') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_sampah'] ?? '-') ?>
                            </td>
                            <td>
                                <?= number_format((float)$row['berat'], 2, ',', '.') ?>
                            </td>
                            <td>
                                Rp <?= number_format((float)$row['harga_per_kg'], 0, ',', '.') ?>
                            </td>
                            <td>
                                Rp <?= number_format((float)$row['total_harga'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <span class="status">
                                    <?= htmlspecialchars($row['status'] ?? 'Diproses') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="empty">
                <p>Belum ada riwayat setoran.</p>
                <p>Riwayat akan muncul setelah kamu melakukan setoran sampah.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>