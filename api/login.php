<?php
header("Content-Type: text/plain; charset=UTF-8");

require_once __DIR__ . "/koneksi.php";

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    http_response_code(400);
    echo "GAGAL|Username dan password wajib diisi";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, nama, username, password, role
     FROM users
     WHERE username = ?
     LIMIT 1"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {
    echo $user["role"] . "|" . $user["id"];
} else {
    http_response_code(401);
    echo "GAGAL|Username atau password salah";
}

$stmt->close();
$conn->close();
?>