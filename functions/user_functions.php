<?php
// function validateLogin($username, $password) {
//     // Contoh validasi sederhana
//     // Ganti dengan query ke database sesuai kebutuhan
//     if ($username === 'admin@admin.com' && $password === '123') {
//         return true;
//     }
//     return false;
// }

function validateLogin($username, $password) {
    include __DIR__ . '/../db.php'; // koneksi database

    // Query cek user berdasarkan username/email dan password
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at, updated_at FROM users WHERE (username=? OR email=?) AND password=? LIMIT 1");
    $stmt->bind_param("sss", $username, $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika user ditemukan, return data user
    if ($user = $result->fetch_assoc()) {
        return $user;
    }
    return false;
}

function validateUserInput($user) {
    if (empty($user['username'])) return false;
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) return false;
    if (empty($user['password'])) return false;
    if (empty($user['role'])) return false;
    return true;
}