<?php
require 'koneksi.php';
$id = (int)$_GET['id'];
$res = $conn->query("SELECT * FROM kategori_artikel WHERE id = $id");
echo json_encode($res->fetch_assoc());
?>