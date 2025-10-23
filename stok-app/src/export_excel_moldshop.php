<?php
include 'config_moldshop.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Rekap_Stok_Moldshop_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['ID', 'Nama Barang', 'Model', 'Stok Awal', 'Barang Masuk', 'Barang Keluar', 'Sisa Stok', 'Tanggal Update']);

// Ambil data
$result = $conn->query("SELECT * FROM items");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['nama_barang'],
        $row['model'],
        $row['stok_awal'],
        $row['barang_masuk'],
        $row['barang_keluar'],
        $row['sisa_stok'],
        $row['tanggal_update']
    ]);
}

fclose($output);
exit;
?>
