<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Simpan data ke session
        $_SESSION['id']       = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

// require_once 'functions/user_functions.php';

// if (isset($_POST['login'])) {
//     $username = $_POST['username'];
//     $password = $_POST['password'];

//     if (validateLogin($username, $password)) {
//         $_SESSION['username'] = $username;
//         $_SESSION['role'] = 'user'; // atau ambil dari database
//         header('Location: admin_dashboard.php');
//         exit;
//     } else {
//         $error = "Username atau password salah!";
//     }
// }

?>

<!DOCTYPE html>
<html>
<head>
  <title>Sekolah Ku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow p-4" style="width: 400px;">
    <h4 class="text-center mb-3">🔐 Login Sekolah Alam</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <div class="input-group">
          <input type="password" name="password" id="passwordInput" class="form-control" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
              <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
            </svg>
          </button>
        </div>
      </div>

      <button name="login" class="btn btn-primary w-100">Masuk</button>
    </form>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('passwordInput');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');

    const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>`;
    const eyeSlashIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>`;

    togglePasswordBtn.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';
        
        // Toggle input type
        passwordInput.type = isPassword ? 'text' : 'password';
        
        // Toggle icon
        this.innerHTML = isPassword ? eyeSlashIcon : eyeIcon;
    });
});
</script>

</body>
</html>
