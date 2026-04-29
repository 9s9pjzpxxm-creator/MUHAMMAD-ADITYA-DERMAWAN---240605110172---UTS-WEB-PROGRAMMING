<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id_penulis'];
    $nama_depan = htmlspecialchars(trim($_POST['nama_depan']));
    $nama_belakang = htmlspecialchars(trim($_POST['nama_belakang']));
    $user_name = htmlspecialchars(trim($_POST['user_name']));
    
    $query = "UPDATE penulis SET nama_depan=?, nama_belakang=?, user_name=?";
    $params = [$nama_depan, $nama_belakang, $user_name];
    $types = "sss";

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password_hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $query .= ", password=?";
        $params[] = $password_hashed;
        $types .= "s";
    }

    // Ambil nama foto lama
    $get_foto = $conn->query("SELECT foto FROM penulis WHERE id = $id");
    if ($get_foto->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Gagal: Penulis tidak ditemukan."]);
        exit;
    }
    $foto_lama = $get_foto->fetch_assoc()['foto'];

    $dir_upload = __DIR__ . "/uploads_penulis/";

    // Jika ada foto baru diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ukuran = $_FILES['foto']['size'];
        $nama_asli = $_FILES['foto']['name'];

        if ($ukuran <= 2 * 1024 * 1024) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            if (in_array($mime, ['image/jpeg', 'image/png'])) {
                $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
                $nama_foto_baru = uniqid('profil_', true) . '.' . $ekstensi;
                $tujuan = $dir_upload . $nama_foto_baru;

                if (move_uploaded_file($tmp_name, $tujuan)) {
                    $query .= ", foto=?";
                    $params[] = $nama_foto_baru;
                    $types .= "s";

                    // Hapus foto lama jika bukan default.png dan file ada
                    if ($foto_lama != 'default.png' && file_exists($dir_upload . $foto_lama)) {
                        unlink($dir_upload . $foto_lama);
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
        echo json_encode(["status" => "success", "message" => "Data penulis berhasil diperbarui!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui data penulis di database."]);
    }
    $stmt->close();
}
$conn->close();
?>