<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// --- Tambah Data ---
if (isset($_POST['save'])) {
    $stmt = $conn->prepare("INSERT INTO courses (course, mentor, title) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['course'], $_POST['mentor'], $_POST['title']);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// --- Hapus Data ---
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// --- Ambil data untuk form edit ---
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
}

// --- Update Data ---
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE courses SET course = ?, mentor = ?, title = ? WHERE id = ?");
    $stmt->bind_param("sssi", $_POST['course'], $_POST['mentor'], $_POST['title'], $_POST['id']);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// --- Pagination Setup ---
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data untuk pagination
$totalDataRes = $conn->query("SELECT COUNT(*) AS total FROM courses");
$totalData = $totalDataRes->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Kelola Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

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

<h3><?= $editData ? 'Edit Course' : 'Tambah Courses' ?></h3>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <?= $editData ? 'Edit User' : 'Tambah User'; ?>
    </div>
    <div class="card-body">

<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="id" value="<?= isset($editData['id']) ? $editData['id'] : '' ?>">
  <div class="col-md-3">
    <input type="text" name="course" class="form-control" placeholder="Nama Course" value="<?= isset($editData['course']) ? htmlspecialchars($editData['course']) : '' ?>" required>
  </div>
  <div class="col-md-3">
    <input type="text" name="mentor" class="form-control" placeholder="Mentor" value="<?= isset($editData['mentor']) ? htmlspecialchars($editData['mentor']) : '' ?>">
  </div>
  <div class="col-md-3">
    <input type="text" name="title" class="form-control" placeholder="Title (Dr./S.Kom/M.T.)" value="<?= isset($editData['title']) ? htmlspecialchars($editData['title']) : '' ?>">
  </div>
  <div class="col-md-3">
    <?php if ($editData): ?>
      <button class="btn btn-warning" name="update">Update</button>
      <a href="courses.php" class="btn btn-secondary">Batal</a>
    <?php else: ?>
      <button class="btn btn-success" name="save">Tambah</button>
    <?php endif; ?>
  </div>
</form>

<table class="table table-bordered table-striped table-hover align-middle">
  <tr class="table-primary text-center">
  <thead class="table-primary text-center">
    <th>No</th>
    <th>Course</th>
    <th>Mentor</th>
    <th>Title</th>
    <th>Aksi</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $no = $start + 1;
  $data = $conn->query("SELECT * FROM courses ORDER BY id DESC LIMIT $start, $limit");
  while ($c = $data->fetch_assoc()) {
    echo "<tr>
      <td class='text-center'>$no</td>
      <td>$c[course]</td>
      <td>$c[mentor]</td>
      <td>$c[title]</td>
      <td class='text-center'>
        <a href='?edit=$c[id]' class='btn btn-warning btn-sm'>Edit</a>
        <a href='?delete=$c[id]' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus course ini?');\">Hapus</a>
      </td>
    </tr>";
    $no++;
  }
  ?>
  </tbody>
</table>

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
</body>
</html>
