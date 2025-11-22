<?php
header('Content-Type: application/json');
include '../db.php';

$sql = "
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

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => $data
], JSON_PRETTY_PRINT);