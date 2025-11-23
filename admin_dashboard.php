<?php
session_start();
include 'db.php';

// Cek login dan role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/* =========================================================================
    1. Grafik: Jumlah peserta didik untuk setiap mata kuliah
   ========================================================================= */
$query1 = "
    SELECT 
        c.course AS course,
        c.mentor AS mentor,
        c.title AS title,
        COUNT(uc.id_user) AS jumlah_peserta
    FROM 
        userCourse uc
    JOIN 
        courses c ON uc.id_course = c.id
    GROUP BY 
        c.course, c.mentor, c.title
    ORDER BY 
        c.id ASC
";
$result1 = $conn->query($query1);

$courseNames = [];
$participantCounts = [];
while ($row = $result1->fetch_assoc()) {
    $courseNames[] = $row['course'];
    $participantCounts[] = $row['jumlah_peserta'];
}

/* =========================================================================
   2. Grafik: Total fee mentor (Rp 2.000.000 × jumlah peserta)
   ========================================================================= */
$query2 = "
    SELECT 
        c.mentor,
        COUNT(uc.id_user) AS jumlah_peserta,
        COUNT(uc.id_user) * 2000000 AS total_fee
    FROM 
        courses c
    JOIN 
        userCourse uc ON c.id = uc.id_course
    JOIN 
        users u ON uc.id_user = u.id
    GROUP BY 
        c.mentor
    ORDER BY 
        total_fee DESC, c.mentor ASC
";
$result2 = $conn->query($query2);

$mentorNames = [];
$totalFees = [];
$participantMentorCounts = [];
while ($row = $result2->fetch_assoc()) {
    $mentorNames[] = $row['mentor'];
    $participantMentorCounts[] = $row['jumlah_peserta'];
    $totalFees[] = $row['total_fee'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light p-4">

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

  <!-- 📈 Grafik 1: Jumlah Peserta per Mata Kuliah -->
  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
      📊 Jumlah Peserta Didik per Mata Kuliah
    </div>
    <div class="card-body">
      <canvas id="courseChart" height="120"></canvas>
    </div>
  </div>

  <!-- 💰 Grafik 2: Total Fee per Mentor -->
  <div class="card shadow mb-4">
    <div class="card-header bg-success text-white">
      💰 Total Fee per Mentor (Rp)
    </div>
    <div class="card-body">
      <canvas id="mentorChart" height="120"></canvas>
    </div>
  </div>

  <!-- <a href="index.php" class="btn btn-secondary mt-3">⬅️ Kembali ke Beranda</a> -->
</div>

<script>
// === Grafik 1: Jumlah Peserta per Mata Kuliah ===
new Chart(document.getElementById('courseChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($courseNames) ?>,
        datasets: [{
            label: 'Jumlah Peserta Didik',
            data: <?= json_encode($participantCounts) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Jumlah Peserta' } },
            x: { title: { display: true, text: 'Mata Kuliah' } }
        },
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Jumlah Peserta Didik per Mata Kuliah' }
        }
    }
});

// === Grafik 2: Total Fee per Mentor ===
new Chart(document.getElementById('mentorChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($mentorNames) ?>,
        datasets: [
            {
                label: 'Jumlah Peserta',
                data: <?= json_encode($participantMentorCounts) ?>,
                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            },
            {
                label: 'Total Fee (Rp)',
                data: <?= json_encode($totalFees) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        indexAxis: 'y', // horizontal bar
        scales: {
            x: {
                beginAtZero: true,
                title: { display: true, text: 'Nilai (Rp)' },
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            },
            y: { title: { display: true, text: 'Mentor' } }
        },
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Total Fee dan Jumlah Peserta per Mentor' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label.includes('Fee')) {
                            return 'Total: Rp ' + context.parsed.x.toLocaleString('id-ID');
                        }
                        return context.dataset.label + ': ' + context.parsed.x;
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>
