<?php
session_start();
include 'db.php';

// Cek apakah user sudah login dan role-nya adalah 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .course-card {
      border-radius: 12px;
      transition: all 0.3s ease;
    }
    .course-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="p-4">

<div class="container">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>👋 Halo, <?= $username ?>!</h3>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>

  <!-- Bagian 1: Mata Kuliah yang Diambil -->
  <div class="mb-5">
    <h4 class="mb-3 text-success">🎓 Mata Kuliah yang Kamu Ambil</h4>

    <?php
    $ambil = $conn->query("
        SELECT c.course, c.mentor, c.title
        FROM userCourse uc
        JOIN courses c ON uc.id_course = c.id
        WHERE uc.id_user = '$user_id'
    ");

    if ($ambil->num_rows > 0) {
        echo "<div class='row'>";
        while ($row = $ambil->fetch_assoc()) {
            echo "
            <div class='col-md-4 mb-4'>
              <div class='card border-success course-card h-100'>
                <div class='card-body'>
                  <h5 class='card-title text-success fw-bold'>{$row['course']}</h5>
                  <p class='card-text mb-1'><strong>Mentor:</strong> {$row['mentor']}</p>
                  <p class='card-text mb-2'><strong>Title:</strong> {$row['title']}</p>
                </div>
              </div>
            </div>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>Kamu belum mengambil mata kuliah apa pun.</div>";
    }
    ?>
  </div>

  <!-- Bagian 2: Semua Mata Kuliah Tersedia -->
  <div>
    <h4 class="mb-3 text-primary">📚 Daftar Semua Mata Kuliah</h4>

    <div class="row">
      <?php
      $result = $conn->query("SELECT * FROM courses ORDER BY id ASC");
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "
              <div class='col-md-4 mb-4'>
                <div class='card course-card shadow-sm h-100'>
                  <div class='card-body'>
                    <h5 class='card-title text-primary fw-bold'>{$row['course']}</h5>
                    <p class='card-text mb-1'><strong>Mentor:</strong> {$row['mentor']}</p>
                    <p class='card-text mb-2'><strong>Title:</strong> {$row['title']}</p>
                    <form method='post' action='daftar_course.php'>
                      <input type='hidden' name='id_course' value='{$row['id']}'>
                      <button type='submit' class='btn btn-outline-primary btn-sm'>Daftar</button>
                    </form>
                  </div>
                </div>
              </div>
              ";
          }
      } else {
          echo "<div class='col-12'><div class='alert alert-warning'>Belum ada mata kuliah tersedia.</div></div>";
      }
      ?>
    </div>
  </div>
</div>

</body>
</html>
