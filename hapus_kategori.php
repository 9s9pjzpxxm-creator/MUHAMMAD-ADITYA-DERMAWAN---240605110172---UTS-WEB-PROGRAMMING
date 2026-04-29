<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // 1. Cek apakah kategori masih dipakai di tabel artikel
    $cek_relasi = $conn->prepare("SELECT id FROM artikel WHERE id_kategori = ? LIMIT 1");
    $cek_relasi->bind_param("i", $id);
    $cek_relasi->execute();
    $cek_relasi->store_result();

    if ($cek_relasi->num_rows > 0) {
        // Jika ada artikel terkait, tolak penghapusan 
        echo json_encode(["status" => "error", "message" => "Gagal: Kategori masih digunakan oleh artikel!"]);
    } else {
        // 2. Jika aman, hapus kategori [cite: 112, 135]
        $stmt = $conn->prepare("DELETE FROM kategori_artikel WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Kategori berhasil dihapus."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan pada database."]);
        }
        $stmt->close();
    }
    
    $cek_relasi->close();
}
$conn->close();
?>