<?php
session_start();
include 'config_maintenance.php';

// === AUTO REKAP SETIAP AWAL BULAN ===
date_default_timezone_set('Asia/Jakarta');

// Ambil bulan & tahun sekarang
$bulan_ini = date('m');
$tahun_ini = date('Y');
$periode_sekarang = "$tahun_ini-$bulan_ini";

// Ambil bulan sebelumnya
$bulan_lalu = $bulan_ini - 1;
$tahun_lalu = $tahun_ini;
if ($bulan_lalu == 0) {
    $bulan_lalu = 12;
    $tahun_lalu--;
}
$periode_lalu = sprintf("%04d-%02d", $tahun_lalu, $bulan_lalu);

// Cek apakah bulan lalu sudah direkap
$cek_rekap = $conn->query("SELECT * FROM rekap_bulanan WHERE periode = '$periode_lalu' LIMIT 1");

if ($cek_rekap->num_rows == 0) {
    // Ambil semua barang
    $result = $conn->query("SELECT * FROM items");
    while ($row = $result->fetch_assoc()) {
        $item_id = $row['id'];
        $nama_barang = $row['nama_barang'];
        $stok_awal = $row['stok_awal'];
        $barang_masuk = $row['barang_masuk'];
        $barang_keluar = $row['barang_keluar'];
        $stok_akhir = $row['sisa_stok'];

        // Simpan rekap ke tabel rekap_bulanan
        $insert = $conn->prepare("INSERT INTO rekap_bulanan 
            (bulan, periode, item_id, nama_barang, stok_awal, barang_masuk, barang_keluar, stok_akhir)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("isissiii", $tahun_lalu, $periode_lalu, $item_id, $nama_barang, $stok_awal, $barang_masuk, $barang_keluar, $stok_akhir);
        $insert->execute();

        // Update stok_awal = stok_akhir untuk bulan baru
        $conn->query("UPDATE items 
                      SET stok_awal = $stok_akhir, 
                          barang_masuk = 0, 
                          barang_keluar = 0, 
                          tanggal_update = CURDATE()
                      WHERE id = $item_id");
    }

    // Log
    $notif = "✅ Rekap bulan $periode_lalu berhasil dibuat otomatis dan stok bulan $periode_sekarang diperbarui!";
} else {
    $notif = "";
}

$result = $conn->query("SELECT * FROM items ORDER BY id ASC");
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Maintenance | Kontrol Stok Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stylemoldshop.css">
  <link rel="stylesheet" href="stylecard.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h4>Maintenance Panel</h4>
    <ul>
      <li><a href="dashboard_maintenance.php">📦 Data Barang</a></li>
      <li><a href="add_item_maintenance.php">➕ Tambah Barang</a></li>
      <li><a href="history_maintenance.php">🧾 History Perubahan</a></li>
      <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>

  <!-- Konten utama -->
  <div class="content">
    <?php if (!empty($notif)): ?>
  <div class="alert alert-success"><?= $notif ?></div>
<?php endif; ?>
    <h2 class="d-flex justify-content-between align-items-center">
  📦 Data Stok Barang - Maintenance
  <a href="export_excel_maintenance.php" class="btn btn-success btn-sm">
    ⬇️ Export CSV
  </a>
</h2>
    <hr>

    <?php
// === Ambil batas minimum stok dari database ===
$setting = $conn->query("SELECT batas_minimum FROM setting_stok LIMIT 1")->fetch_assoc();
$batas_minimum = $setting ? (int)$setting['batas_minimum'] : 5;

// === Hitung total data ===
$data = $conn->query("
  SELECT 
    COUNT(*) AS total_barang,
    SUM(sisa_stok) AS total_stok,
    SUM(CASE WHEN sisa_stok < $batas_minimum THEN 1 ELSE 0 END) AS stok_minimum
  FROM items
")->fetch_assoc();
?>

<div class="row text-center mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white p-3 rounded-3 shadow-sm">
      <h5>Total Jenis Barang</h5>
      <h2><?= number_format($data['total_barang']) ?></h2>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-success text-white p-3 rounded-3 shadow-sm">
      <h5>Total Stok Barang</h5>
      <h2><?= number_format($data['total_stok']) ?></h2>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-danger text-white p-3 rounded-3 shadow-sm">
      <h5>Barang Di Bawah Minimum (&lt; <?= $batas_minimum ?>)</h5>
      <h2><?= number_format($data['stok_minimum']) ?></h2>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-warning text-dark p-3 rounded-3 shadow-sm">
      <h5>Batas Minimum (Setting)</h5>
      <h2><?= $batas_minimum ?></h2>
    </div>
  </div>
</div>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Barang</th>
          <th>Model</th>
          <th>Stok Awal</th>
          <th>Masuk</th>
          <th>Keluar</th>
          <th>Sisa Stok</th>
          <th>Tanggal Update</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nama_barang']) ?></td>
              <td><?= htmlspecialchars($row['model']) ?></td>
              <td><?= $row['stok_awal'] ?></td>
              <td><?= $row['barang_masuk'] ?></td>
              <td><?= $row['barang_keluar'] ?></td>
              <td><?= $row['sisa_stok'] ?></td>
              <td><?= $row['tanggal_update'] ?></td>
              <td>
            <a href="edit_item_maintenance.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
            <a href="delete_item_maintenance.php?id=<?= $row['id']; ?>"
              class="btn btn-danger btn-sm" onclick="return confirm('⚠️ Yakin ingin menghapus barang ini? Semua data terkait di history & rekap bulanan juga akan dihapus!');">
              🗑️ Delete
            </a>
          </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center">Belum ada data barang</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
