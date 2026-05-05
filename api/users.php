<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';
$myId = (int)$_SESSION['user_id'];

$stmt = $conn->prepare('
    SELECT id, COALESCE(NULLIF(username, \'\'), usuario) AS username
    FROM usuarios
    WHERE id <> ?
    ORDER BY username ASC
');
$stmt->bind_param('i', $myId);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['ok' => true, 'users' => $users]);
$conn->close();
?>
