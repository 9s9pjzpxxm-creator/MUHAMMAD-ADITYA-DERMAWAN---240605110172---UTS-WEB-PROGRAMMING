<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $cek_relasi = $conn->prepare("SELECT id FROM artikel WHERE id_penulis = ? LIMIT 1");
    $cek_relasi->bind_param("i", $id);
    $cek_relasi->execute();
    $cek_relasi->store_result();

    if ($cek_relasi->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Gagal: Penulis tidak bisa dihapus karena masih memiliki artikel!"]);
    } else {
        // PERBAIKAN: Ambil nama foto sebelum dihapus
        $get_foto = $conn->prepare("SELECT foto FROM penulis WHERE id = ?");
        $get_foto->bind_param("i", $id);
        $get_foto->execute();
        $res_foto = $get_foto->get_result();
        $foto = $res_foto->fetch_assoc()['foto'];
        $get_foto->close();

        $stmt = $conn->prepare("DELETE FROM penulis WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Hapus file fisik
            if ($foto && $foto != 'default.png' && file_exists("uploads_penulis/" . $foto)) {
                unlink("uploads_penulis/" . $foto);
            }
            echo json_encode(["status" => "success", "message" => "Data penulis berhasil dihapus."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan sistem."]);
        }
        $stmt->close();
    }
    $cek_relasi->close();
}
$conn->close();
?>