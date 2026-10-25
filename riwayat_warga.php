
<?php
require_once __DIR__ . '/api/koneksi.php';

function aman($teks) {
    return htmlspecialchars((string)($teks ?? ''), ENT_QUOTES, 'UTF-8');
}

$idWarga = (int)($_GET['id'] ?? 0);

if ($idWarga <= 0) {
    die("ID warga tidak valid. Buka halaman dengan parameter ?id=3");
}

// Ambil data warga
$stmt = $conn->prepare(
    "SELECT id, nama, username, kode_warga
     FROM users
     WHERE id = ? AND role = 'warga'
     LIMIT 1"
);
$stmt->bind_param("i", $idWarga);
$stmt->execute();
$warga = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$warga) {
    die("Data warga tidak ditemukan.");
}

// Ambil riwayat setoran tanpa JOIN tabel jenis_sampah
$stmt = $conn->prepare(
    "SELECT id, tanggal, jenis_sampah_id, berat,
            harga_per_kg, total_harga, status
     FROM setoran
     WHERE warga_id = ?
     ORDER BY id DESC"
);
$stmt->bind_param("i", $idWarga);
$stmt->execute();
$riwayat = $stmt->get_result();

$totalSetoran = 0;
$totalBerat = 0;

while ($data = $riwayat->fetch_assoc()) {
    $totalSetoran++;
    $totalBerat += (float)$data['berat'];
    $daftarRiwayat[] = $data;
}
$stmt->close();

$daftarRiwayat = $daftarRiwayat ?? [];
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
    padding: 24px;
    background: #101827;
    color: #f1f5f9;
    font-family: Arial, sans-serif;
}
.container {
    max-width: 1000px;
    margin: auto;
}
.header {
    margin-bottom: 24px;
}
.header h1 {
    margin-bottom: 8px;
    color: #4ade80;
}
.header p {
    color: #cbd5e1;
}
.card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 20px;
}
.info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.label {
    color: #94a3b8;
    font-size: 13px;
    margin-bottom: 7px;
}
.value {
    font-size: 16px;
    font-weight: bold;
    overflow-wrap: anywhere;
}
.summary {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.summary-item {
    flex: 1;
    min-width: 150px;
    background: #0f172a;
    padding: 16px;
    border-radius: 10px;
}
.summary-item strong {
    display: block;
    color: #4ade80;
    font-size: 24px;
    margin-top: 8px;
}
.table-wrap {
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}
th, td {
    padding: 13px 12px;
    border-bottom: 1px solid #334155;
    text-align: left;
}
th {
    color: #4ade80;
    background: #0f172a;
}
.badge {
    display: inline-block;
    padding: 6px 10px;
    background: #334155;
    border-radius: 20px;
    font-size: 12px;
}
.empty {
    text-align: center;
    padding: 30px;
    color: #94a3b8;
}
.back {
    display: inline-block;
    padding: 11px 16px;
    background: #16a34a;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 18px;
}
@media (max-width: 600px) {
    body {
        padding: 14px;
    }
    .card {
        padding: 14px;
    }
}
</style>
</head>
<body>
<div class="container">

    <a class="back" href="javascript:history.back()">← Kembali</a>

    <div class="header">
        <h1>♻️ Riwayat Setoran Warga</h1>
        <p>Bank Sampah Warga</p>
    </div>

    <div class="card">
        <h2>Informasi Warga</h2>
        <div class="info">
            <div>
                <div class="label">Nama Warga</div>
                <div class="value"><?= aman($warga['nama']) ?></div>
            </div>
            <div>
                <div class="label">Kode Warga</div>
                <div class="value"><?= aman($warga['kode_warga']) ?></div>
            </div>
            <div>
                <div class="label">Username</div>
                <div class="value"><?= aman($warga['username']) ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Ringkasan Setoran</h2>
        <div class="summary">
            <div class="summary-item">
                Total Setoran
                <strong><?= $totalSetoran ?></strong>
            </div>
            <div class="summary-item">
                Total Berat
                <strong><?= number_format($totalBerat, 2, ',', '.') ?> kg</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Daftar Riwayat</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>ID Jenis Sampah</th>
                        <th>Berat</th>
                        <th>Harga/Kg</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($daftarRiwayat)): ?>
                    <tr>
                        <td colspan="7" class="empty">
                            Belum ada riwayat setoran.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarRiwayat as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= aman($row['tanggal']) ?></td>
                        <td><?= aman($row['jenis_sampah_id']) ?></td>
                        <td><?= number_format((float)$row['berat'], 2, ',', '.') ?> kg</td>
                        <td>Rp<?= number_format((float)$row['harga_per_kg'], 0, ',', '.') ?></td>
                        <td>Rp<?= number_format((float)$row['total_harga'], 0, ',', '.') ?></td>
                        <td><span class="badge"><?= aman($row['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
