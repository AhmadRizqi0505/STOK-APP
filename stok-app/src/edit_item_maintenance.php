<?php
include 'config_maintenance.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_barang'];
    $model = $_POST['model'];
    $stok_awal = (int)$_POST['stok_awal'];
    $barang_masuk = (int)$_POST['barang_masuk'];
    $barang_keluar = (int)$_POST['barang_keluar'];
    $keterangan = $_POST['keterangan'];

    // Update data tanpa menyentuh kolom sisa_stok
    $stmt = $conn->prepare("
        UPDATE items 
        SET nama_barang=?, model=?, stok_awal=?, barang_masuk=?, barang_keluar=?, tanggal_update=NOW()
        WHERE id=?
    ");
    $stmt->bind_param("ssiiii", $nama, $model, $stok_awal, $barang_masuk, $barang_keluar, $id);
    $stmt->execute();

    // Ambil sisa stok hasil generate otomatis
    $result = $conn->query("SELECT sisa_stok FROM items WHERE id = $id");
    $row = $result->fetch_assoc();
    $sisa_stok = $row['sisa_stok'];

    // Catat ke history pergerakan stok
    $stmt2 = $conn->prepare("
        INSERT INTO stock_movements (item_id, tanggal, stok_awal, barang_masuk, barang_keluar, keterangan)
        VALUES (?, NOW(), ?, ?, ?, ?)
    ");
    $stmt2->bind_param("iiiis", $id, $stok_awal, $barang_masuk, $barang_keluar, $keterangan);
    $stmt2->execute();

    header("Location: dashboard_maintenance.php");
    exit();
}

// Ambil data barang untuk ditampilkan di form edit
$result = $conn->query("SELECT * FROM items WHERE id = $id");
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Barang - Maintenance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h3 class="text-center mb-4">✏️ Edit Barang - Maintenance</h3>
    <form method="POST">
      <div class="mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" value="<?= htmlspecialchars($item['nama_barang']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Model</label>
        <input type="text" name="model" value="<?= htmlspecialchars($item['model']) ?>" class="form-control">
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label>Stok Awal</label>
          <input type="number" name="stok_awal" value="<?= $item['stok_awal'] ?>" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Barang Masuk</label>
          <input type="number" name="barang_masuk" value="<?= $item['barang_masuk'] ?>" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Barang Keluar</label>
          <input type="number" name="barang_keluar" value="<?= $item['barang_keluar'] ?>" class="form-control">
        </div>
      </div>
      <div class="mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2"></textarea>
      </div>
      <button type="submit" class="btn btn-primary w-100">💾 Simpan Perubahan</button>
      <a href="dashboard_maintenance.php" class="btn btn-secondary w-100 mt-2">⬅️ Kembali ke Dashboard</a>
    </form>
  </div>
</div>
</body>
</html>
