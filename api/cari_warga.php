<?php
require_once __DIR__ . "/koneksi.php";

header("Content-Type: text/plain; charset=UTF-8");

// Menerima parameter kode_warga dari aplikasi
$kode = trim($_GET["kode_warga"] ?? "");

if ($kode === "") {
    exit("GAGAL|Kode warga belum diisi");
}

// Mengubah pencarian menjadi berdasarkan kolom 'id' karena di database menggunakan id
$stmt = $conn->prepare(
    "SELECT id, nama, username
     FROM users
     WHERE id = ? AND role = 'warga'
     LIMIT 1"
);

$stmt->bind_param("s", $kode);
$stmt->execute();

$result = $stmt->get_result();

if ($warga = $result->fetch_assoc()) {
    // Mengembalikan format dengan pemisah '|'
    echo "BERHASIL|" . $warga["id"] . "|" .
         $warga["nama"] . "|" . $warga["username"];
} else {
    echo "GAGAL|Warga tidak ditemukan";
}

$stmt->close();
$conn->close();
?>