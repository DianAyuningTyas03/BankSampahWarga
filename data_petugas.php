<?php
require_once __DIR__ . "/api/koneksi.php";

$query = $conn->query(
    "SELECT id, nama, username
     FROM users
     WHERE role = 'petugas'
     ORDER BY id DESC"
);

if (!$query) {
    die("Gagal mengambil data petugas.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas | Bank Sampah</title>

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
            padding: 22px 5%;
        }

        header a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        header h1 {
            font-size: 25px;
            margin-top: 20px;
        }

        header p {
            font-size: 13px;
            margin-top: 8px;
            color: #dcfce7;
        }

        main {
            max-width: 1000px;
            width: 90%;
            margin: 25px auto;
        }

        .panel {
            background: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 4px 15px #00000008;
        }

        .panel h2 {
            font-size: 19px;
            margin-bottom: 8px;
        }

        .count {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            white-space: nowrap;
        }

        th {
            background: #f0fdf4;
            color: #166534;
        }

        .number {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .empty {
            text-align: center;
            padding: 30px 10px;
            color: #64748b;
            line-height: 1.7;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 22px;
            }

            .panel {
                padding: 16px;
            }

            th, td {
                padding: 12px 10px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="login_admin.php">← Kembali ke Dashboard</a>
    <h1>🧑‍💼 Data Petugas</h1>
    <p>Daftar akun petugas Bank Sampah Warga</p>
</header>

<main>
    <section class="panel">
        <h2>Daftar Petugas</h2>

        <p class="count">
            Total petugas:
            <strong><?= $query->num_rows ?></strong>
        </p>

        <?php if ($query->num_rows > 0): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Petugas</th>
                            <th>Username</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($petugas = $query->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="number">
                                        <?= $no++ ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($petugas['nama']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($petugas['username']) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="empty">
                <div style="font-size:35px">🧑‍💼</div>
                <p>Belum ada data petugas.</p>
            </div>

        <?php endif; ?>

    </section>
</main>

</body>
</html>