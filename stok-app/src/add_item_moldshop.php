<?php
include 'config_moldshop.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_barang'];
    $model = $_POST['model'];
    $stok_awal = (int)$_POST['stok_awal'];
    $barang_masuk = (int)$_POST['barang_masuk'];
    $barang_keluar = (int)$_POST['barang_keluar'];
    $keterangan = $_POST['keterangan'];

    // Simpan data ke tabel items (tanpa sisa_stok karena dihitung otomatis)
    $stmt = $conn->prepare("
        INSERT INTO items (nama_barang, model, stok_awal, barang_masuk, barang_keluar, tanggal_update) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssiii", $nama, $model, $stok_awal, $barang_masuk, $barang_keluar);
    $stmt->execute();
    $item_id = $stmt->insert_id;

    // Ambil sisa_stok hasil perhitungan otomatis
    $result = $conn->query("SELECT sisa_stok FROM items WHERE id = $item_id");
    $row = $result->fetch_assoc();
    $sisa_stok = $row['sisa_stok'];

    // Simpan ke tabel history (stock_movements)
    $stmt2 = $conn->prepare("
        INSERT INTO stock_movements (item_id, tanggal, stok_awal, barang_masuk, barang_keluar, keterangan)
        VALUES (?, NOW(), ?, ?, ?, ?)
    ");
    $stmt2->bind_param("iiiis", $item_id, $stok_awal, $barang_masuk, $barang_keluar, $keterangan);
    $stmt2->execute();

    header("Location: dashboard_moldshop.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Barang | Moldshop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h3 class="text-center mb-4">Tambah Barang - Moldshop</h3>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control">
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Stok Awal</label>
          <input type="number" name="stok_awal" class="form-control" value="0">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Barang Masuk</label>
          <input type="number" name="barang_masuk" class="form-control" value="0">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Barang Keluar</label>
          <input type="number" name="barang_keluar" class="form-control" value="0">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2" placeholder="Misal: stok awal bulan, barang baru..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100">💾 Simpan Barang</button>
      <a href="dashboard_moldshop.php" class="btn btn-secondary w-100 mt-2">⬅️ Kembali ke Dashboard</a>
    </form>
  </div>
</div>
</body>
</html>
