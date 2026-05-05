<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';
$myId = (int)$_SESSION['user_id'];
$receiverUsername = trim($_GET['receiver'] ?? '');

if ($receiverUsername === '') {
    echo json_encode(['ok' => false, 'message' => 'Usuario destino requerido']);
    exit;
}

$target = $conn->prepare('SELECT id FROM usuarios WHERE username = ? OR usuario = ? LIMIT 1');
$target->bind_param('ss', $receiverUsername, $receiverUsername);
$target->execute();
$receiver = $target->get_result()->fetch_assoc();

if (!$receiver) {
    echo json_encode(['ok' => false, 'message' => 'Usuario destino no existe']);
    exit;
}

$otherId = (int)$receiver['id'];

$stmt = $conn->prepare(
    'SELECT m.mensaje, m.created_at AS enviado_en, COALESCE(NULLIF(u.username, \'\'), u.usuario) AS emisor_username
     FROM mensajes m
     INNER JOIN usuarios u ON u.id = m.sender_id
     WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
     ORDER BY m.created_at ASC'
);
$stmt->bind_param('iiii', $myId, $otherId, $otherId, $myId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode([
    'ok' => true,
    'messages' => $messages,
    'my_username' => $_SESSION['username']
]);

$conn->close();
?>
