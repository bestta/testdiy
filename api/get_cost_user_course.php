<?php
header('Content-Type: application/json');
include '../db.php';

$sql = "
SELECT 
    c.mentor,
    COUNT(uc.id_user) AS jumlah_peserta,
    COUNT(uc.id_user) * 2000000 AS total_fee
FROM 
    courses c
JOIN 
    usercourse uc ON c.id = uc.id_course
JOIN 
    users u ON uc.id_user = u.id
GROUP BY 
    c.mentor
ORDER BY 
    total_fee DESC, c.mentor ASC
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