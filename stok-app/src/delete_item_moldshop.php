<?php
include 'config_moldshop.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Hapus semua relasi data terkait barang ini
    $conn->query("DELETE FROM stock_movements WHERE item_id = $id");
    $conn->query("DELETE FROM rekap_bulanan WHERE item_id = $id");

    // Hapus barang utama
    $delete = $conn->query("DELETE FROM items WHERE id = $id");

    if ($delete) {
        header("Location: dashboard_moldshop.php?status=deleted");
        exit;
    } else {
        echo "❌ Gagal menghapus data: " . $conn->error;
    }
} else {
    header("Location: dashboard_moldshop.php");
    exit;
}
?>
