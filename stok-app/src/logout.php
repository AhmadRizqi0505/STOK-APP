<?php
session_start();
session_unset();     // Hapus semua data sesi
session_destroy();   // Hancurkan sesi yang aktif

// Arahkan kembali ke halaman login
header("Location: login.php");
exit();
?>
