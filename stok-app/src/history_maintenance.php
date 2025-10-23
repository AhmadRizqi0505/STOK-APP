<?php
include 'config_maintenance.php';

// Ambil semua data dari tabel stock_movements dan relasi ke items
$sql = "SELECT s.id, i.nama_barang, i.model, s.tanggal, s.stok_awal, s.barang_masuk, s.barang_keluar, 
               (s.stok_awal + s.barang_masuk - s.barang_keluar) AS sisa_stok, 
               s.keterangan
        FROM stock_movements s
        JOIN items i ON s.item_id = i.id
        ORDER BY s.tanggal DESC, s.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>History Stok | Maintenance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stylemoldshop.css">
</head>
<body class="bg-light">

<!-- Sidebar sederhana -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard_maintenance.php">Maintenance</a>
    <div>
      <li><a href="dashboard_maintenance.php">📦 Data Barang</a></li>
      <li><a href="add_item_maintenance.php">➕ Tambah Barang</a></li>
      <li><a href="history_maintenance.php" class="active">🧾 History Perubahan</a></li>
      <li><a href="export_excel_maintenance.php">📤 Export ke Excel</a></li>
      <li><a href="logout.php">🚪 Logout</a></li>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white text-center">
      <h4>📜 Riwayat Perubahan Stok Barang - Maintenance</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Nama Barang</th>
              <th>Model</th>
              <th>Stok Awal</th>
              <th>Masuk</th>
              <th>Keluar</th>
              <th>Sisa Stok</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                  <td><?= htmlspecialchars($row['model']) ?></td>
                  <td class="text-center"><?= $row['stok_awal'] ?></td>
                  <td class="text-success text-center fw-bold"><?= $row['barang_masuk'] ?></td>
                  <td class="text-danger text-center fw-bold"><?= $row['barang_keluar'] ?></td>
                  <td class="text-center fw-semibold"><?= $row['sisa_stok'] ?></td>
                  <td><?= htmlspecialchars($row['keterangan']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center text-muted">Belum ada data history stok</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
