<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['id']);

// Ambil id_course dengan kompatibilitas PHP lama
$course_id = isset($_POST['id_course']) ? intval($_POST['id_course']) : 0;

if ($course_id <= 0) {
    echo "<script>alert('Gagal: course tidak ditemukan.'); window.location='user_dashboard.php';</script>";
    exit();
}

// ------------------------------
// Helper: tampilkan error dan redirect
function fail_and_redirect($msg) {
    $msg = addslashes($msg);
    echo "<script>alert('Gagal: {$msg}'); window.location='user_dashboard.php';</script>";
    exit();
}

// Pastikan koneksi valid
if (!isset($conn) || !($conn instanceof mysqli)) {
    fail_and_redirect('Koneksi database tidak tersedia.');
}

// 1) Cek apakah course ada
$sqlChkCourse = "SELECT id FROM courses WHERE id = ? LIMIT 1";
if (!$stmt = $conn->prepare($sqlChkCourse)) {
    fail_and_redirect("Prepare cek course gagal: " . $conn->error);
}
if (!$stmt->bind_param("i", $course_id)) {
    $stmt->close();
    fail_and_redirect("Bind param cek course gagal: " . $conn->error);
}
if (!$stmt->execute()) {
    $stmt->close();
    fail_and_redirect("Execute cek course gagal: " . $conn->error);
}
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    fail_and_redirect('Mata kuliah tidak ditemukan.');
}
$stmt->close();

// 2) Cek apakah user sudah terdaftar di course ini
$sqlCheckUserCourse = "SELECT id_user FROM userCourse WHERE id_user = ? AND id_course = ? LIMIT 1";
if (!$stmt = $conn->prepare($sqlCheckUserCourse)) {
    fail_and_redirect("Prepare cek relasi gagal: " . $conn->error);
}
if (!$stmt->bind_param("ii", $user_id, $course_id)) {
    $stmt->close();
    fail_and_redirect("Bind param cek relasi gagal: " . $conn->error);
}
if (!$stmt->execute()) {
    $stmt->close();
    fail_and_redirect("Execute cek relasi gagal: " . $conn->error);
}
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('Kamu sudah terdaftar di mata kuliah ini!'); window.location='user_dashboard.php';</script>";
    exit();
}
$stmt->close();

// 3) Insert relasi userCourse
$sqlInsert = "INSERT INTO userCourse (id_user, id_course) VALUES (?, ?)";
if (!$stmt = $conn->prepare($sqlInsert)) {
    fail_and_redirect("Prepare insert gagal: " . $conn->error);
}
if (!$stmt->bind_param("ii", $user_id, $course_id)) {
    $stmt->close();
    fail_and_redirect("Bind param insert gagal: " . $conn->error);
}
if (!$stmt->execute()) {
    $stmt->close();
    fail_and_redirect("Execute insert gagal: " . $conn->error);
}
$stmt->close();

echo "<script>alert('Berhasil mendaftar mata kuliah!'); window.location='user_dashboard.php';</script>";
exit();
?>
