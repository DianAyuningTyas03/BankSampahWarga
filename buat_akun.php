<?php
require_once __DIR__ . "/api/koneksi.php";

$akun = [
    [
        "nama" => "Administrator",
        "username" => "admin",
        "password" => "admin123",
        "role" => "admin"
    ],
    [
        "nama" => "Petugas",
        "username" => "petugas",
        "password" => "petugas123",
        "role" => "petugas"
    ],
    [
        "nama" => "Warga",
        "username" => "warga",
        "password" => "warga123",
        "role" => "warga"
    ]
];

$stmt = $conn->prepare(
    "INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, ?)"
);

foreach ($akun as $a) {
    $nama = $a["nama"];
    $username = $a["username"];
    $password = password_hash($a["password"], PASSWORD_DEFAULT);
    $role = $a["role"];

    $stmt->bind_param(
        "ssss",
        $nama,
        $username,
        $password,
        $role
    );

    if (!$stmt->execute()) {
        die("Gagal membuat akun: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();

echo "Berhasil! Akun Admin, Petugas, dan Warga sudah dibuat.";
?>