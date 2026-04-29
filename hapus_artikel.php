<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Ambil nama file gambar sebelum data dihapus dari database
    $get_gambar = $conn->prepare("SELECT gambar FROM artikel WHERE id = ?");
    $get_gambar->bind_param("i", $id);
    $get_gambar->execute();
    $result = $get_gambar->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $nama_gambar = $row['gambar'];
        $path_gambar = "uploads_artikel/" . $nama_gambar;

        // Hapus data dari database
        $stmt = $conn->prepare("DELETE FROM artikel WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Hapus file fisik gambar dari folder jika file-nya ada
            if (file_exists($path_gambar) && is_file($path_gambar)) {
                unlink($path_gambar);
            }
            echo json_encode(["status" => "success", "message" => "Artikel dan gambar berhasil dihapus."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus artikel dari database."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Data artikel tidak ditemukan."]);
    }
    $get_gambar->close();
}
$conn->close();
?>