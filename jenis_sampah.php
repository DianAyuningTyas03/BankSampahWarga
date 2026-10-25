<?php
require_once __DIR__ . "/api/koneksi.php";

$pesan = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama_sampah"] ?? "");
    $harga = $_POST["harga_per_kg"] ?? "";

    if ($nama === "" || $harga === "" || !is_numeric($harga) || $harga < 0) {
        $error = "Nama sampah dan harga yang valid wajib diisi.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO jenis_sampah (nama_sampah, harga_per_kg)
             VALUES (?, ?)"
        );

        $harga = (float) $harga;
        $stmt->bind_param("sd", $nama, $harga);

        if ($stmt->execute()) {
            header("Location: jenis_sampah.php?status=berhasil");
            exit;
        } else {
            $error = "Data gagal ditambahkan.";
        }

        $stmt->close();
    }
}

if (($_GET["status"] ?? "") === "berhasil") {
    $pesan = "Jenis sampah berhasil ditambahkan!";
}

$query = $conn->query(
    "SELECT id, nama_sampah, harga_per_kg
     FROM jenis_sampah
     ORDER BY id DESC"
);

if (!$query) {
    die("Gagal mengambil data jenis sampah.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#15803d">
    <title>Jenis Sampah | Bank Sampah</title>

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
            font-size: 13px;
            margin-top: 8px;
            color: #dcfce7;
        }

        main {
            width: 90%;
            max-width: 950px;
            margin: 25px auto;
        }

        .panel {
            background: white;
            padding: 22px;
            border-radius: 17px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px #00000008;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 17px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            display: block;
            width: 100%;
            padding: 13px;
            margin-bottom: 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #15803d;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #15803d;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #166534;
        }

        .message {
            padding: 13px;
            border-radius: 10px;
            margin-bottom: 17px;
            font-size: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 14px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            background: #f0fdf4;
            color: #166534;
        }

        .price {
            color: #15803d;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 25px 5px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        @media (max-width: 500px) {
            header h1 {
                font-size: 22px;
            }

            .panel {
                padding: 17px;
            }

            th, td {
                padding: 12px 8px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="login_admin.php">← Kembali ke Dashboard</a>
    <h1>♻️ Jenis Sampah</h1>
    <p>Kelola jenis sampah dan harga per kilogram.</p>
</header>

<main>

    <section class="panel">
        <h2>➕ Tambah Jenis Sampah</h2>

        <?php if ($pesan !== ""): ?>
            <div class="message success">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="jenis_sampah.php">
            <label for="nama_sampah">Nama Sampah</label>
            <input
                type="text"
                id="nama_sampah"
                name="nama_sampah"
                placeholder="Contoh: Botol Plastik"
                maxlength="100"
                required
            >

            <label for="harga_per_kg">Harga per Kilogram (Rp)</label>
            <input
                type="number"
                id="harga_per_kg"
                name="harga_per_kg"
                placeholder="Contoh: 3000"
                min="0"
                step="any"
                required
            >

            <button type="submit">Simpan Jenis Sampah</button>
        </form>
    </section>

    <section class="panel">
        <h2>📋 Daftar Jenis Sampah</h2>

        <?php if ($query->num_rows > 0): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Sampah</th>
                            <th>Harga/Kg</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($sampah = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($sampah["nama_sampah"]) ?>
                                </td>

                                <td class="price">
                                    Rp <?= number_format((float)$sampah["harga_per_kg"], 0, ",", ".") ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="empty">
                <div style="font-size: 35px;">♻️</div>
                <p>Belum ada jenis sampah.</p>
                <p>Tambahkan jenis sampah melalui formulir di atas.</p>
            </div>

        <?php endif; ?>
    </section>

</main>

</body>
</html>