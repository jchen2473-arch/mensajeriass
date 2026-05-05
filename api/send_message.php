<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';
$senderId = (int)$_SESSION['user_id'];
$receiverUsername = trim($_POST['receiver'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($receiverUsername === '' || $message === '') {
    echo json_encode(['ok' => false, 'message' => 'Faltan datos para enviar']);
    exit;
}

$target = $conn->prepare('SELECT id FROM usuarios WHERE username = ? OR usuario = ? LIMIT 1');
$target->bind_param('ss', $receiverUsername, $receiverUsername);
$target->execute();
$receiver = $target->get_result()->fetch_assoc();

if (!$receiver) {
    echo json_encode(['ok' => false, 'message' => 'Solo puedes enviar a cuentas existentes']);
    exit;
}

$receiverId = (int)$receiver['id'];

if ($receiverId === $senderId) {
    echo json_encode(['ok' => false, 'message' => 'No puedes enviarte mensajes a ti mismo']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO mensajes (sender_id, receiver_id, mensaje) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $senderId, $receiverId, $message);

if (!$stmt->execute()) {
    echo json_encode(['ok' => false, 'message' => 'No se pudo enviar el mensaje']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Mensaje enviado']);
$conn->close();
?>
