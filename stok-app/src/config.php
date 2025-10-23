<?php
session_start(); // penting supaya bisa ambil data divisi dari session

$host = 'db';
$user = 'root';
$pass = 'secret';

// Cek apakah user sudah login dan punya divisi
if (isset($_SESSION['divisi'])) {
    if ($_SESSION['divisi'] === 'moldshop') {
        $dbname = 'stok_moldshop';
    } elseif ($_SESSION['divisi'] === 'maintenance') {
        $dbname = 'stok_maintenance';
    } else {
        die("Divisi tidak dikenal!");
    }
} else {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
