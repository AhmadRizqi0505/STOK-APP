<?php
$host = 'db';
$user = 'root';
$pass = 'secret';
$dbname = 'stok_moldshop';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi Moldshop gagal: " . $conn_moldshop->connect_error);
}
?>
