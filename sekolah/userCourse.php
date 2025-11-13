<?php
include 'db.php';

// --- Tambah atau Edit relasi ---
if (isset($_POST['save'])) {
  $id_user = $_POST['id_user'];
  $id_course = $_POST['id_course'];
  $id = isset($_POST['id']) ? $_POST['id'] : null;

  if ($id) {
    $stmt = $conn->prepare("UPDATE usercourse SET id_user=?, id_course=? WHERE id=?");
    $stmt->bind_param("iii", $id_user, $id_course, $id);
    $stmt->execute();
  } else {
    $stmt = $conn->prepare("INSERT INTO usercourse(id_user, id_course) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_user, $id_course);
    $stmt->execute();
  }
  header("Location: userCourse.php");
  exit;
}

// --- Hapus relasi ---
if (isset($_GET['delete'])) {
  $id_delete = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM usercourse WHERE id=?");
  $stmt->bind_param("i", $id_delete);
  $stmt->execute();
  header("Location: userCourse.php");
  exit;
}

// --- Ambil data untuk edit ---
$editData = null;
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $res = $conn->query("SELECT * FROM usercourse WHERE id = $id_edit");
  if ($res) {
    $editData = $res->fetch_assoc();
  }
}

// --- Pagination Setup ---
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$totalDataRes = $conn->query("SELECT COUNT(*) AS total FROM usercourse");
$totalData = $totalDataRes->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data sesuai halaman
$relasi = $conn->query("
    SELECT uc.id_user, u.username, c.course
    FROM usercourse uc
    JOIN users u ON uc.id_user = u.id
    JOIN courses c ON uc.id_course = c.id
    ORDER BY u.id, c.id
    LIMIT $start, $limit
");

if (!$relasi) {
  die("Query Error (relasi): " . $conn->error);
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Relasi User-Course</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
  <h3>🔗 Relasi User-Course</h3>

  <!-- Form Tambah/Edit -->
  <form method="post" class="row g-2 mb-4">
    <input type="hidden" name="id" value="<?php echo isset($editData['id']) ? $editData['id'] : ''; ?>">

    <div class="col-md-4">
      <select name="id_user" class="form-select" required>
        <option value="">Pilih User</option>
        <?php
        $u = $conn->query("SELECT * FROM users");
        while ($us = $u->fetch_assoc()) {
          $selected = ($editData && $editData['id_user'] == $us['id']) ? 'selected' : '';
          echo "<option value='" . $us['id'] . "' " . $selected . ">" . $us['username'] . "</option>";
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
          $selected = ($editData && $editData['id_course'] == $cs['id']) ? 'selected' : '';
          echo "<option value='" . $cs['id'] . "' " . $selected . ">" . $cs['course'] . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-4">
      <button class="btn btn-success" name="save"><?php echo $editData ? 'Update' : 'Tambah'; ?></button>
      <?php if ($editData): ?>
        <a href="userCourse.php" class="btn btn-secondary">Batal</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabel Data -->
  <table class="table table-bordered table-striped">
    <tr class="table-primary text-center">
      <th>No</th>
      <th>Username</th>
      <th>Course</th>
      <th>Aksi</th>
    </tr>
    <?php
    $no = $start + 1;
    while ($r = $relasi->fetch_assoc()) {
      echo "<tr>
        <td>" . $no . "</td>
        <td>" . $r['username'] . "</td>
        <td>" . $r['course'] . "</td>
        <td class='text-center'>
          <a href='?edit=" . $r['id_user'] . "' class='btn btn-warning btn-sm'>Edit</a>
          <a href='?delete=" . $r['id_user'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin hapus data ini?');\">Hapus</a>
        </td>
      </tr>";
      $no++;
    }
    ?>
  </table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">⬅️ Kembali ke Dashboard Admin</a>
</body>

</html>