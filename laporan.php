<?php
require_once __DIR__ . "/api/koneksi.php";

// Fitur unduh data Excel
if (isset($_GET["export"]) && $_GET["export"] === "excel") {
    $sqlExcel = "
        SELECT
            s.id,
            w.nama AS nama_warga,
            p.nama AS nama_petugas,
            j.nama_sampah,
            s.berat,
            s.total_harga,
            s.tanggal,
            s.status
        FROM setoran s
        LEFT JOIN users w ON s.warga_id = w.id
        LEFT JOIN users p ON s.petugas_id = p.id
        LEFT JOIN jenis_sampah j ON s.jenis_sampah_id = j.id
        ORDER BY s.id DESC
    ";

    $dataExcel = $conn->query($sqlExcel);

    if (!$dataExcel) {
        die("Gagal mengambil data untuk Excel.");
    }

    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header('Content-Disposition: attachment; filename="laporan_setoran.xls"');

    echo "\xEF\xBB\xBF";
    echo '<table border="1">';
    echo '<tr>
        <th>No.</th>
        <th>Nama Warga</th>
        <th>Nama Petugas</th>
        <th>Jenis Sampah</th>
        <th>Berat (kg)</th>
        <th>Total Harga</th>
        <th>Tanggal</th>
        <th>Status</th>
    </tr>';

    $no = 1;

    while ($row = $dataExcel->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row["nama_warga"] ?? "Tidak ditemukan", ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars($row["nama_petugas"] ?? "Belum ditentukan", ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars($row["nama_sampah"] ?? "Tidak ditemukan", ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars((string)$row["berat"], ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars((string)$row["total_harga"], ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars((string)$row["tanggal"], ENT_QUOTES, "UTF-8") . '</td>';
        echo '<td>' . htmlspecialchars($row["status"] ?? "Belum ada", ENT_QUOTES, "UTF-8") . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    $conn->close();
    exit;
}

// Ringkasan data setoran
$sqlRingkasan = "
    SELECT
        COUNT(*) AS jumlah_setoran,
        COALESCE(SUM(berat), 0) AS total_berat,
        COALESCE(SUM(total_harga), 0) AS total_harga
    FROM setoran
";

$resultRingkasan = $conn->query($sqlRingkasan);

if (!$resultRingkasan) {
    die("Gagal mengambil ringkasan laporan.");
}

$ringkasan = $resultRingkasan->fetch_assoc();

// Rincian setoran
$sqlLaporan = "
    SELECT
        s.id,
        w.nama AS nama_warga,
        p.nama AS nama_petugas,
        j.nama_sampah,
        s.berat,
        s.total_harga,
        s.tanggal,
        s.status
    FROM setoran s
    LEFT JOIN users w ON s.warga_id = w.id
    LEFT JOIN users p ON s.petugas_id = p.id
    LEFT JOIN jenis_sampah j ON s.jenis_sampah_id = j.id
    ORDER BY s.id DESC
";

$resultLaporan = $conn->query($sqlLaporan);

if (!$resultLaporan) {
    die("Gagal mengambil rincian laporan.");
}

function formatRupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ",", ".");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#15803d">
    <title>Laporan | Bank Sampah Warga</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7f4;
            color: #1f2937;
        }

        header {
            background: #15803d;
            color: white;
            padding: 22px 5% 30px;
            border-radius: 0 0 24px 24px;
        }

        header a {
            color: #dcfce7;
            text-decoration: none;
            font-size: 14px;
        }

        header h1 {
            font-size: 25px;
            margin-top: 22px;
        }

        header p {
            color: #dcfce7;
            font-size: 13px;
            line-height: 1.6;
            margin-top: 8px;
        }

        main {
            width: 90%;
            max-width: 1100px;
            margin: 25px auto;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            border: 1px solid #e5eee6;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 15px #00000008;
        }

        .icon {
            font-size: 25px;
            margin-bottom: 14px;
        }

        .card p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .card h2 {
            color: #15803d;
            font-size: 24px;
            overflow-wrap: anywhere;
        }

        .panel {
            background: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 4px 15px #00000008;
        }

        .panel h2 {
            font-size: 19px;
            margin-bottom: 18px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 13px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        th {
            background: #f0fdf4;
            color: #166534;
        }

        .empty {
            padding: 30px 10px;
            text-align: center;
            color: #64748b;
            line-height: 1.8;
        }

        .hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 14px;
        }

        .button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .print-button,
        .excel-button {
            display: inline-block;
            color: white;
            padding: 12px 16px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .print-button {
            background: #15803d;
        }

        .print-button:hover {
            background: #166534;
        }

        .excel-button {
            background: #166534;
        }

        .excel-button:hover {
            background: #14532d;
        }

        @media (max-width: 650px) {
            header h1 {
                font-size: 22px;
            }

            .stats {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .card {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 17px;
            }

            .icon {
                margin-bottom: 0;
                flex-shrink: 0;
            }

            .card p {
                margin-bottom: 6px;
            }

            .card h2 {
                font-size: 22px;
            }

            .panel {
                padding: 16px;
            }
        }

        @media print {
            body {
                background: white;
            }

            header {
                background: white;
                color: black;
                padding: 10px 0;
            }

            header a,
            .button-row,
            .hint {
                display: none;
            }

            header p {
                color: #333;
            }

            main {
                width: 100%;
                margin: 10px 0;
            }

            .card,
            .panel {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .stats {
                grid-template-columns: repeat(3, 1fr);
            }

            table {
                min-width: 0;
            }

            th, td {
                font-size: 10px;
                padding: 7px 5px;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="login_admin.php">← Kembali ke Dashboard</a>
    <h1>📊 Laporan Bank Sampah</h1>
    <p>Ringkasan dan rincian transaksi setoran sampah.</p>
</header>

<main>

    <div class="button-row">
        <button class="print-button" onclick="window.print()">
            🖨️ Cetak Laporan
        </button>

        <a class="excel-button" href="laporan.php?export=excel">
            📊 Cetak Excel
        </a>
    </div>

    <section class="stats">

        <div class="card">
            <div class="icon">📦</div>
            <div>
                <p>Total Setoran</p>
                <h2><?= (int)$ringkasan["jumlah_setoran"] ?></h2>
            </div>
        </div>

        <div class="card">
            <div class="icon">⚖️</div>
            <div>
                <p>Total Berat Sampah</p>
                <h2>
                    <?= number_format((float)$ringkasan["total_berat"], 2, ",", ".") ?>
                    kg
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="icon">💰</div>
            <div>
                <p>Total Nilai Setoran</p>
                <h2><?= formatRupiah($ringkasan["total_harga"]) ?></h2>
            </div>
        </div>

    </section>

    <section class="panel">
        <h2>Rincian Transaksi</h2>

        <?php if ($resultLaporan->num_rows > 0): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Warga</th>
                            <th>Petugas</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Total Harga</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($row = $resultLaporan->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row["nama_warga"] ?? "Tidak ditemukan") ?></td>
                                <td><?= htmlspecialchars($row["nama_petugas"] ?? "Belum ditentukan") ?></td>
                                <td><?= htmlspecialchars($row["nama_sampah"] ?? "Tidak ditemukan") ?></td>
                                <td><?= number_format((float)$row["berat"], 2, ",", ".") ?></td>
                                <td><?= formatRupiah($row["total_harga"]) ?></td>
                                <td><?= htmlspecialchars((string)$row["tanggal"]) ?></td>
                                <td><?= htmlspecialchars($row["status"] ?? "Belum ada") ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <p class="hint">
                Geser tabel ke samping jika dibuka melalui HP.
            </p>

        <?php else: ?>

            <div class="empty">
                <div style="font-size: 38px;">📊</div>
                <p><strong>Belum ada transaksi setoran.</strong></p>
                <p>Laporan akan terisi otomatis setelah ada data setoran.</p>
            </div>

        <?php endif; ?>
    </section>

</main>

</body>
</html>