<?php
require_once __DIR__ . "/api/koneksi.php";

$sql = "
    SELECT
        s.id,
        w.nama AS nama_warga,
        p.nama AS nama_petugas,
        j.nama_sampah,
        s.berat,
        s.harga_per_kg,
        s.total_harga,
        s.tanggal,
        s.status,
        s.foto_path
    FROM setoran s
    LEFT JOIN users w ON s.warga_id = w.id
    LEFT JOIN users p ON s.petugas_id = p.id
    LEFT JOIN jenis_sampah j ON s.jenis_sampah_id = j.id
    ORDER BY s.id DESC
";

$query = $conn->query($sql);

if (!$query) {
    die("Gagal mengambil data setoran. Periksa struktur tabel database.");
}

function rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ",", ".");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#15803d">
    <title>Data Setoran | Bank Sampah</title>

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
            padding: 22px 5% 28px;
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
            margin-top: 8px;
        }

        main {
            width: 90%;
            max-width: 1100px;
            margin: 25px auto;
        }

        .summary {
            background: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px #00000008;
        }

        .summary p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary h2 {
            color: #15803d;
            font-size: 28px;
        }

        .panel {
            background: white;
            padding: 22px;
            border-radius: 16px;
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
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            text-align: left;
            padding: 13px 11px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        th {
            background: #f0fdf4;
            color: #166534;
        }

        .status {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 6px 9px;
            border-radius: 20px;
            font-size: 12px;
        }

        .foto-sampah {
            width: 80px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            display: block;
        }

        .foto-tidak-tersedia {
            color: #64748b;
            font-size: 12px;
            white-space: nowrap;
        }

        .empty {
            text-align: center;
            padding: 35px 10px;
            color: #64748b;
            line-height: 1.8;
        }

        .hint {
            color: #64748b;
            font-size: 12px;
            margin-top: 14px;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 22px;
            }

            .panel {
                padding: 16px;
            }

            .summary h2 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="login_admin.php">← Kembali ke Dashboard</a>
    <h1>📦 Data Setoran</h1>
    <p>Pantau aktivitas setoran Bank Sampah Warga.</p>
</header>

<main>
    <section class="summary">
        <p>Total data setoran</p>
        <h2><?= $query->num_rows ?></h2>
    </section>

    <section class="panel">
        <h2>📋 Riwayat Setoran</h2>

        <?php if ($query->num_rows > 0): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Warga</th>
                            <th>Petugas</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Harga/kg</th>
                            <th>Total Harga</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Foto Sampah</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row["nama_warga"] ?? "Tidak ditemukan") ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["nama_petugas"] ?? "Belum ditentukan") ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["nama_sampah"] ?? "Tidak ditemukan") ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)$row["berat"]) ?>
                                </td>

                                <td>
                                    <?= rupiah($row["harga_per_kg"]) ?>
                                </td>

                                <td>
                                    <?= rupiah($row["total_harga"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)$row["tanggal"]) ?>
                                </td>

                                <td>
                                    <span class="status">
                                        <?= htmlspecialchars($row["status"] ?? "Belum ada") ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $foto = trim((string)($row["foto_path"] ?? ""));
                                    ?>

                                    <?php if ($foto !== ""): ?>
                                        <a
                                            href="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <img
                                                class="foto-sampah"
                                                src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                                alt="Foto sampah"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                                            >
                                            <span
                                                class="foto-tidak-tersedia"
                                                style="display:none;"
                                            >
                                                Foto tidak ditemukan
                                            </span>
                                        </a>
                                    <?php else: ?>
                                        <span class="foto-tidak-tersedia">
                                            Belum ada foto
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <p class="hint">
                Geser tabel ke samping untuk melihat semua kolom di HP.
            </p>

        <?php else: ?>

            <div class="empty">
                <div style="font-size: 38px;">📦</div>
                <p><strong>Belum ada data setoran.</strong></p>
                <p>Data akan muncul setelah setoran tersimpan di database.</p>
            </div>

        <?php endif; ?>
    </section>
</main>

</body>
</html>