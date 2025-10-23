<?php
$host = 'db';
$user = 'root';
$pass = 'secret';
$dbname = 'stok_maintenance';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi Maintenance gagal: " . $conn->connect_error);
}
?>
