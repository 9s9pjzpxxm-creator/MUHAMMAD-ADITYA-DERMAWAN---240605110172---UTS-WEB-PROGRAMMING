<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kategori = htmlspecialchars(trim($_POST['nama_kategori']));
    $keterangan = htmlspecialchars(trim($_POST['keterangan']));

    if (!empty($nama_kategori)) {
        // Prepared statement untuk keamanan
        $stmt = $conn->prepare("INSERT INTO kategori_artikel (nama_kategori, keterangan) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_kategori, $keterangan);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Kategori berhasil ditambahkan."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan data."]);
        }
        $stmt->close();
    }
}
$conn->close();
?>