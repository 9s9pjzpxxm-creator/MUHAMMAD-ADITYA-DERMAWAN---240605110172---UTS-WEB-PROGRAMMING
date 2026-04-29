<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_depan = htmlspecialchars(trim($_POST['nama_depan']));
    $nama_belakang = htmlspecialchars(trim($_POST['nama_belakang']));
    $user_name = htmlspecialchars(trim($_POST['user_name']));
    $password_raw = trim($_POST['password']);
    
    // Validasi input dasar
    if (empty($user_name) || empty($password_raw)) {
        echo json_encode(["status" => "error", "message" => "Gagal: Username dan Password wajib diisi."]);
        exit;
    }

    $password_hashed = password_hash($password_raw, PASSWORD_BCRYPT);
    $nama_foto = "default.png"; // Default jika tidak upload foto

    $dir_upload = __DIR__ . "/uploads_penulis/";

    // Jika ada foto diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ukuran = $_FILES['foto']['size'];
        $nama_asli = $_FILES['foto']['name'];
        
        // Cek ukuran (maks 2MB)
        if ($ukuran <= 2 * 1024 * 1024) {
            // Cek tipe file (MIME)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            if (in_array($mime, ['image/jpeg', 'image/png'])) {
                // CEK JIKA FOLDER BELUM DIBUAT
                if (!is_dir($dir_upload)) {
                    echo json_encode(["status" => "error", "message" => "FALAL: Folder 'uploads_penulis' belum kamu buat secara manual!"]);
                    exit;
                }

                $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
                $nama_foto = uniqid('profil_', true) . '.' . $ekstensi;
                $tujuan = $dir_upload . $nama_foto;

                if (!move_uploaded_file($tmp_name, $tujuan)) {
                    // Gagal pindah file, gunakan default
                    $nama_foto = "default.png";
                }
            }
        }
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO penulis (nama_depan, nama_belakang, user_name, password, foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_depan, $nama_belakang, $user_name, $password_hashed, $nama_foto);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data penulis berhasil ditambahkan!"]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode(["status" => "error", "message" => "Username sudah digunakan, pilih yang lain."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan data penulis ke database."]);
        }
    }
    $stmt->close();
}
$conn->close();
?>