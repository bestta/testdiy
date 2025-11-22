<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Koneksi database
include '../db.php';

try {
    // Soal No 6
    $sql = "
        SELECT 
            u.id AS id,
            u.username,
            c.course,
            c.mentor,
            c.title
        FROM 
            userCourse uc
        JOIN 
            users u ON uc.id_user = u.id
        JOIN 
            courses c ON uc.id_course = c.id
        WHERE 
            c.mentor IN ('Ari', 'Darren')
        ORDER BY 
            u.id, c.id
    ";

    $result = $conn->query($sql);
    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id'       => (int)$row['id'],
                'username' => $row['username'],
                'course'   => $row['course'],
                'mentor'   => $row['mentor'],
                'title'    => $row['title']
            ];
        }

        echo json_encode([
            'status' => 'success',
            'count'  => count($data),
            'data'   => $data
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'success',
            'count'  => 0,
            'data'   => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
