<?php

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db.php'; // koneksi ke DB

// ➕ Tambah Data User
if (isset($_POST['save'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if ($username && $email && $password && $role) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at, updated_at)
                                VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('✅ User berhasil ditambahkan!'); window.location='users.php';</script>";
        } else {
            echo "<script>alert('❌ Gagal menambahkan user!');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('⚠️ Semua field harus diisi!');</script>";
    }
}


// 🧠 Ambil data user untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM users WHERE id='$id'");
    $editData = $res->fetch_assoc();
}

// ✏️ Update Data User
if (isset($_POST['update'])) {
    $id       = $_POST['id'];
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if (!empty($password)) {
        // Jika password diisi → simpan apa adanya
        $stmt = $conn->prepare("UPDATE users 
                                SET username = ?, email = ?, password = ?, role = ?, updated_at = NOW() 
                                WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $password, $role, $id);
    } else {
        // Jika password kosong → jangan ubah password
        $stmt = $conn->prepare("UPDATE users 
                                SET username = ?, email = ?, role = ?, updated_at = NOW() 
                                WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('✅ User berhasil diperbarui!'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal memperbarui user!');</script>";
    }

    $stmt->close();
}


// ❌ Hapus Data User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id='$id'");
    echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
}

// --- Pagination Setup ---
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data untuk pagination
$totalDataRes = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalData = $totalDataRes->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="container">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>👋 Selamat Datang, <span class="text-primary"><?= htmlspecialchars($_SESSION['username']) ?></span> (Admin)</h3>
    <div>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <!-- Navigasi Management -->
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white">
      ⚙️ Menu Manajemen Sistem
    </div>
    <div class="card-body d-flex flex-wrap gap-3">
       <a href="admin_dashboard.php" class="btn btn-outline-warning btn-lg">
        📈 Dashboard
      </a>
      <a href="users.php" class="btn btn-outline-primary btn-lg">
        👤 Kelola Users
      </a>
      <a href="courses.php" class="btn btn-outline-success btn-lg">
        📚 Kelola Courses
      </a>
      <a href="userCourse.php" class="btn btn-outline-warning btn-lg">
        🧾 Kelola User Courses
      </a>
    </div>
  </div>

  <!-- <h3 class="text-center mb-4">👤 Kelola Users</h3> -->
  <h3>Kelola Users</h3>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <?= $editData ? 'Edit User' : 'Tambah User'; ?>
    </div>
    <div class="card-body">
  <form method="post" class="row g-2 mb-4">
    <input type="hidden" name="id" value="<?php echo isset($editData['id']) ? $editData['id'] : ''; ?>">

    <div class="col-md-2">
      <input type="text" name="username" class="form-control" placeholder="Username"
             value="<?php echo isset($editData['username']) ? $editData['username'] : ''; ?>" required>
    </div>
    <div class="col-md-3">
      <input type="email" name="email" class="form-control" placeholder="Email"
             value="<?php echo isset($editData['email']) ? $editData['email'] : ''; ?>" required>
    </div>
    <div class="col-md-3">
      <div class="input-group">
        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password"
              value="<?php echo isset($editData['password']) ? $editData['password'] : ''; ?>" <?php if (!$editData) echo 'required'; ?>>
        <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
          👁️
        </button>
      </div>
    </div>
    <div class="col-md-2">
      <select name="role" class="form-select" required>
        <option value="">Pilih Role</option>
        <option value="user" <?php echo (isset($editData['role']) && $editData['role'] == 'user') ? 'selected' : ''; ?>>User</option>
        <option value="admin" <?php echo (isset($editData['role']) && $editData['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
      </select>
    </div>
    <div class="col-md-2">
      <?php if ($editData): ?>
        <button type="submit" name="update" class="btn btn-warning w-100">Update</button>
        <a href="users.php" class="btn btn-secondary w-100 mt-2">Batal</a>
      <?php else: ?>
        <button type="submit" name="save" class="btn btn-success w-100">Tambah</button>
      <?php endif; ?>
    </div>
  </form>



  <div class="table-responsive">
  <table class="table table-bordered table-striped table-hover align-middle mb-0">
    <thead class="table-primary text-center">
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = $start + 1;
      $result = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT $start, $limit");
      while ($row = $result->fetch_assoc()):
      ?>
      <tr>
        <td class="text-center"><?php echo $no++; ?></td>
        <td class="text-break"><?php echo htmlspecialchars($row['username']); ?></td>
        <td class="text-break"><?php echo htmlspecialchars($row['email']); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($row['role']); ?></td>
        <td class="text-center">
          <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?');">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

  <!-- Pagination -->
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
        </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const togglePasswordBtn = document.getElementById('togglePasswordBtn');
  const passwordInput = document.getElementById('passwordInput');

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', function () {
      // Toggle the type attribute
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);

      // Toggle the icon (optional)
      this.innerHTML = type === 'password' ? '👁️' : '🙈';
    });
  }
});
</script>

</body>
</html>
