<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id_artikel'];
    $judul = htmlspecialchars(trim($_POST['judul']));
    $id_kategori = (int)$_POST['id_kategori'];
    $id_penulis = (int)$_POST['id_penulis'];
    $isi = htmlspecialchars(trim($_POST['isi']));

    $query = "UPDATE artikel SET judul=?, id_kategori=?, id_penulis=?, isi=?";
    $params = [$judul, $id_kategori, $id_penulis, $isi];
    $types = "siis";

    // Ambil nama gambar lama
    $get_gambar = $conn->query("SELECT gambar FROM artikel WHERE id = $id");
    if ($get_gambar->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Gagal: Artikel tidak ditemukan."]);
        exit;
    }
    $row_lama = $get_gambar->fetch_assoc();
    $gambar_lama = $row_lama['gambar'];

    $dir_upload = __DIR__ . "/uploads_artikel/";

    // Jika ada gambar baru yang diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $ukuran = $_FILES['gambar']['size'];
        $nama_asli = $_FILES['gambar']['name'];

        // Validasi Sederhana
        if ($ukuran <= 2 * 1024 * 1024) {
             $finfo = finfo_open(FILEINFO_MIME_TYPE);
             $mime = finfo_file($finfo, $tmp_name);
             finfo_close($finfo);

             if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                 $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
                 $nama_gambar_baru = uniqid('artikel_', true) . '.' . $ekstensi;
                 $tujuan = $dir_upload . $nama_gambar_baru;

                 if (move_uploaded_file($tmp_name, $tujuan)) {
                     // Tambahkan ke query update
                     $query .= ", gambar=?";
                     $params[] = $nama_gambar_baru;
                     $types .= "s";

                     // Hapus gambar lama jika ada dan file-nya ada
                     if (!empty($gambar_lama) && file_exists($dir_upload . $gambar_lama)) {
                         unlink($dir_upload . $gambar_lama);
                     }
                 }
             }
        }
    }

    $query .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Artikel berhasil diperbarui!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui artikel di database."]);
    }
    $stmt->close();
}
$conn->close();
?>