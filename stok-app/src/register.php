<?php
include 'config_auth.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $divisi = $_POST['divisi'];

    // Cek apakah username sudah dipakai
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, role, divisi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $role, $divisi);

        if ($stmt->execute()) {
            $success = "Akun berhasil dibuat! Silakan login.";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register | Kontrol Stok Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stylrregister.css">
</head>
<body>
  <div class="register-container">
    <h2>Daftar Akun</h2>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>

      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Divisi</label>
        <select name="divisi" class="form-select" required>
          <option value="moldshop">Moldshop</option>
          <option value="maintenance">Maintenance</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Daftar</button>
      <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
  </div>
</body>
</html>
