<?php
require 'koneksi.php';

// Atur output agar selalu JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input teks
    if (empty(trim($_POST['judul'])) || empty(trim($_POST['isi']))) {
        echo json_encode(["status" => "error", "message" => "Gagal: Judul dan isi artikel tidak boleh kosong!"]);
        exit;
    }

    $judul = htmlspecialchars(trim($_POST['judul']));
    $id_kategori = (int)$_POST['id_kategori'];
    $id_penulis = (int)$_POST['id_penulis'];
    $isi = htmlspecialchars(trim($_POST['isi']));

    // Set tanggal
    date_default_timezone_set('Asia/Jakarta');
    $sekarang = new DateTime();
    $hari_tanggal = $sekarang->format('Y-m-d H:i:s'); // Format standar MySQL

    // Validasi Gambar (Wajib)
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] != UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Gagal: Gambar artikel wajib diunggah atau ukuran terlalu besar!"]);
        exit;
    }

    // Proses Gambar
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $ukuran = $_FILES['gambar']['size'];
    $nama_asli = $_FILES['gambar']['name'];

    // Cek ukuran (maks 2MB)
    if ($ukuran > 2 * 1024 * 1024) {
        echo json_encode(["status" => "error", "message" => "Gagal: Ukuran gambar maksimal 2 MB."]);
        exit;
    }

    // Cek tipe file (MIME)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo json_encode(["status" => "error", "message" => "Gagal: Format gambar harus JPG, PNG, atau GIF."]);
        exit;
    }

    // Generate nama file unik
    $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
    $nama_gambar = uniqid('artikel_', true) . '.' . $ekstensi;
    
    // Tentukan directory upload absolut
    $dir_upload = __DIR__ . "/uploads_artikel/";

    // CEK JIKA FOLDER BELUM DIBUAT
    if (!is_dir($dir_upload)) {
        echo json_encode(["status" => "error", "message" => "FALAL: Folder 'uploads_artikel' belum kamu buat secara manual! Silakan buat folder kosong tersebut di direktori projectmu."]);
        exit;
    }

    $tujuan = $dir_upload . $nama_gambar;
    
    // Proses pemindahan file
    if (move_uploaded_file($tmp_name, $tujuan)) {
        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO artikel (id_penulis, id_kategori, judul, isi, gambar, hari_tanggal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_penulis, $id_kategori, $judul, $isi, $nama_gambar, $hari_tanggal);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Artikel berhasil dipublikasikan!"]);
        } else {
            // Hapus file yang telanjur terupload jika query gagal
            unlink($tujuan);
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan data ke database. Kesalahan MySQL."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mengunggah file gambar ke server. Masalah izin folder (Permission). Jalankan 'sudo chmod -R 777 uploads_artikel' di terminal."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Akses tidak valid."]);
}
$conn->close();
?>