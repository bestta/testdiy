<?php
session_start();
include 'db.php';

// --- Tambah relasi ---
if (isset($_POST['add'])) {
    $id_user = $_POST['id_user'];
    $id_course = $_POST['id_course'];
    $stmt = $conn->prepare("INSERT INTO usercourse (id_user, id_course) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_user, $id_course);
    $stmt->execute();
    header("Location: userCourse.php");
    exit;
}

// --- Update relasi ---
if (isset($_POST['update'])) {
    $id_user = $_POST['id_user'];
    $id_course = $_POST['id_course'];
    $edit_id = $_POST['edit_id'];
    $stmt = $conn->prepare("UPDATE usercourse SET id_user=?, id_course=? WHERE id_user=? AND id_course=?");
    $stmt->bind_param("iiii", $id_user, $id_course, $edit_id, $_POST['old_course']);
    $stmt->execute();
    header("Location: userCourse.php");
    exit;
}

// --- Delete relasi ---
if (isset($_GET['delete_user']) && isset($_GET['delete_course'])) {
    $del_user = $_GET['delete_user'];
    $del_course = $_GET['delete_course'];
    $stmt = $conn->prepare("DELETE FROM usercourse WHERE id_user=? AND id_course=?");
    $stmt->bind_param("ii", $del_user, $del_course);
    $stmt->execute();
    header("Location: userCourse.php");
    exit;
}

// --- Ambil data untuk edit ---
$editData = null;
if (isset($_GET['edit_user']) && isset($_GET['edit_course'])) {
    $edit_user = $_GET['edit_user'];
    $edit_course = $_GET['edit_course'];
    $stmt = $conn->prepare("SELECT * FROM usercourse WHERE id_user=? AND id_course=?");
    $stmt->bind_param("ii", $edit_user, $edit_course);
    $stmt->execute();
    $resultEdit = $stmt->get_result();
    if ($resultEdit) {
        $editData = $resultEdit->fetch_assoc();
    }
}

// --- Paging setup ---
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$totalDataRes = $conn->query("SELECT COUNT(*) AS total FROM usercourse");
$totalData = $totalDataRes->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data relasi usercourse beserta username dan course sesuai halaman
$query = "
    SELECT uc.id_user, uc.id_course, u.username, c.course
    FROM usercourse uc
    JOIN users u ON uc.id_user = u.id
    JOIN courses c ON uc.id_course = c.id
    ORDER BY uc.id_user ASC
    LIMIT $start, $limit
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola User Course</title>
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



    <h3>Kelola User Course</h3>

    <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <?= $editData ? 'Edit User' : 'Tambah User'; ?>
    </div>
    <div class="card-body">
  <form method="post" class="row g-2 mb-4">

    <!-- Form Tambah -->
    <?php if (!$editData): ?>
    <form method="post" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="id_user" class="form-select" required>
                <option value="">Pilih User</option>
                <?php
                $u = $conn->query("SELECT * FROM users");
                while ($us = $u->fetch_assoc()) {
                    echo "<option value='" . $us['id'] . "'>" . $us['username'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="id_course" class="form-select" required>
                <option value="">Pilih Course</option>
                <?php
                $c = $conn->query("SELECT * FROM courses");
                while ($cs = $c->fetch_assoc()) {
                    echo "<option value='" . $cs['id'] . "'>" . $cs['course'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" name="add">Tambah</button>
        </div>
    </form>
    <?php endif; ?>

    <!-- Form Edit -->
    <?php if ($editData): ?>
    <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="edit_id" value="<?php echo $editData['id_user']; ?>">
        <input type="hidden" name="old_course" value="<?php echo $editData['id_course']; ?>">
        <div class="col-md-4">
            <select name="id_user" class="form-select" required disabled>
                <option value="">Pilih User</option>
                <?php
                $u = $conn->query("SELECT * FROM users");
                while ($us = $u->fetch_assoc()) {
                    $selected = ($editData['id_user'] == $us['id']) ? 'selected' : '';
                    echo "<option value='" . $us['id'] . "' $selected>" . $us['username'] . "</option>";
                }
                ?>
            </select>
            <!-- Kirim id_user lewat input hidden agar tetap terkirim -->
            <input type="hidden" name="id_user" value="<?php echo $editData['id_user']; ?>">
        </div>
        <div class="col-md-4">
            <select name="id_course" class="form-select" required>
                <option value="">Pilih Course</option>
                <?php
                $c = $conn->query("SELECT * FROM courses");
                while ($cs = $c->fetch_assoc()) {
                    $selected = ($editData['id_course'] == $cs['id']) ? 'selected' : '';
                    echo "<option value='" . $cs['id'] . "' $selected>" . $cs['course'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-success" name="update">Update</button>
            <a href="userCourse.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr class="table-primary text-center">
                <th>No</th>
                <th>Username</th>
                <th>Course</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $start + 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td class='text-center'>{$no}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['course']}</td>
                    <td class='text-center'>
                        <a href='?edit_user={$row['id_user']}&edit_course={$row['id_course']}&page={$page}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='?delete_user={$row['id_user']}&delete_course={$row['id_course']}&page={$page}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin hapus data ini?');\">Hapus</a>
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