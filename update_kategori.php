<?php
require 'koneksi.php';
$id = (int)$_POST['id_kategori'];
$nama = htmlspecialchars($_POST['nama_kategori']);
$ket = htmlspecialchars($_POST['keterangan']);

$stmt = $conn->prepare("UPDATE kategori_artikel SET nama_kategori=?, keterangan=? WHERE id=?");
$stmt->bind_param("ssi", $nama, $ket, $id);

if($stmt->execute()) echo json_encode(["status"=>"success", "message"=>"Kategori diperbarui"]);
else echo json_encode(["status"=>"error", "message"=>"Gagal update"]);
?>