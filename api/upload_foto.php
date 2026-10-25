<?php
header("Content-Type: text/plain; charset=UTF-8");

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Gunakan metode POST.");
    }

    $isi_foto = file_get_contents("php://input");

    if ($isi_foto === false || strlen($isi_foto) === 0) {
        throw new Exception("Foto belum diterima.");
    }

    $info_gambar = @getimagesizefromstring($isi_foto);

    if ($info_gambar === false) {
        throw new Exception("File bukan gambar yang valid.");
    }

    $jenis_gambar = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    $mime = $info_gambar["mime"];

    if (!isset($jenis_gambar[$mime])) {
        throw new Exception("Format foto harus JPG, PNG, atau WEBP.");
    }

    $folder_upload = dirname(__DIR__) . "/uploads/";

    if (!is_dir($folder_upload) &&
        !mkdir($folder_upload, 0755, true)) {
        throw new Exception("Folder uploads gagal dibuat.");
    }

    $nama_file = bin2hex(random_bytes(16)) . "." .
                 $jenis_gambar[$mime];

    $lokasi_file = $folder_upload . $nama_file;

    if (file_put_contents($lokasi_file, $isi_foto) === false) {
        throw new Exception("Foto gagal disimpan.");
    }

    echo "SUKSES|uploads/" . $nama_file;

} catch (Throwable $e) {
    http_response_code(400);
    echo "GAGAL|" . $e->getMessage();
}
?>