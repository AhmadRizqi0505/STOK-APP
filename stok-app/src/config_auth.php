<?php
$host = 'db';          
$user = 'root';        
$pass = 'secret';      
$dbname = 'stok_auth'; 

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database auth gagal: " . $conn->connect_error);
}
?>
