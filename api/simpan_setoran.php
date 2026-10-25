<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Gunakan metode POST.");
    }

    $warga_id = (int) ($_POST["warga_id"] ?? 0);
    $petugas_id = (int) ($_POST["petugas_id"] ?? 0);
    $nama_sampah = trim($_POST["jenis_sampah"] ?? "");
    $berat = (float) ($_POST["berat"] ?? 0);
    $foto_path = trim($_POST["foto_path"] ?? "");

    if (
        $warga_id <= 0 ||
        $petugas_id <= 0 ||
        $nama_sampah === "" ||
        !is_numeric($_POST["berat"] ?? null) ||
        $berat <= 0
    ) {
        throw new Exception(
            "Data setoran belum lengkap atau tidak valid."
        );
    }

    // Validasi foto jika foto dikirim dari aplikasi
    if ($foto_path !== "") {
        if (!preg_match(
            '~^uploads/[A-Za-z0-9_-]+\.(jpg|jpeg|png|webp)$~i',
            $foto_path
        )) {
            throw new Exception("Alamat foto tidak valid.");
        }

        $lokasi_foto = dirname(__DIR__) . "/" . $foto_path;

        if (!is_file($lokasi_foto)) {
            throw new Exception(
                "Foto belum ditemukan di server. Silakan unggah ulang."
            );
        }
    } else {
        $foto_path = null;
    }

    // Pastikan warga terdaftar
    $stmt = $conn->prepare(
        "SELECT id FROM users
         WHERE id = ? AND role = 'warga'
         LIMIT 1"
    );
    $stmt->bind_param("i", $warga_id);
    $stmt->execute();

    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception("Data warga tidak ditemukan.");
    }
    $stmt->close();

    // Pastikan petugas terdaftar
    $stmt = $conn->prepare(
        "SELECT id FROM users
         WHERE id = ? AND role = 'petugas'
         LIMIT 1"
    );
    $stmt->bind_param("i", $petugas_id);
    $stmt->execute();

    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception(
            "ID petugas tidak valid. Silakan login kembali."
        );
    }
    $stmt->close();

    // Cari jenis sampah dan harga dari database
    $stmt = $conn->prepare(
        "SELECT id, harga_per_kg
         FROM jenis_sampah
         WHERE nama_sampah = ?
         LIMIT 1"
    );
    $stmt->bind_param("s", $nama_sampah);
    $stmt->execute();

    $jenis = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$jenis) {
        throw new Exception(
            "Jenis sampah tidak ditemukan di database."
        );
    }

    $jenis_sampah_id = (int) $jenis["id"];
    $harga_per_kg = (float) $jenis["harga_per_kg"];
    $total_harga = $berat * $harga_per_kg;
    $status = "menunggu";

    // Simpan setoran beserta alamat foto
    $stmt = $conn->prepare(
        "INSERT INTO setoran
         (warga_id, petugas_id, jenis_sampah_id,
          berat, harga_per_kg, total_harga, status, foto_path)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iiidddss",
        $warga_id,
        $petugas_id,
        $jenis_sampah_id,
        $berat,
        $harga_per_kg,
        $total_harga,
        $status,
        $foto_path
    );

    $stmt->execute();
    $id_setoran = $conn->insert_id;
    $stmt->close();

    echo json_encode([
        "status" => "success",
        "pesan" => "Setoran berhasil disimpan.",
        "id_setoran" => $id_setoran,
        "total_harga" => $total_harga,
        "foto_path" => $foto_path
    ]);

} catch (Throwable $e) {
    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "pesan" => $e->getMessage()
    ]);
}

$conn->close();
?>