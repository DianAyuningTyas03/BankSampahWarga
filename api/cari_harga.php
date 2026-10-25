<?php
require_once __DIR__ . "/koneksi.php";

$nama = trim($_POST["nama_sampah"] ?? "");

if ($nama === "") {
    exit("ERROR|Nama sampah kosong");
}

$stmt = $conn->prepare(
    "SELECT harga_per_kg FROM jenis_sampah WHERE nama_sampah = ? LIMIT 1"
);
$stmt->bind_param("s", $nama);
$stmt->execute();
$stmt->bind_result($harga);

if ($stmt->fetch()) {
    echo "BERHASIL|" . $harga;
} else {
    echo "ERROR|Jenis sampah tidak ditemukan";
}

$stmt->close();
$conn->close();
?>