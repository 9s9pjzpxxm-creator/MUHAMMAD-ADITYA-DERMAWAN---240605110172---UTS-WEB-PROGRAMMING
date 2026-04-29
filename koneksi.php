<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_blog";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . htmlspecialchars($conn->connect_error));
}
?>