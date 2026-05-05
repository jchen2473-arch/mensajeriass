<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connection.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['ok' => false, 'message' => 'Completa todos los campos']);
    exit;
}

$stmt = $conn->prepare('
    SELECT id, COALESCE(NULLIF(username, \'\'), usuario) AS login_name, password_hash
    FROM usuarios
    WHERE username = ? OR usuario = ?
    LIMIT 1
');
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(['ok' => false, 'message' => 'Credenciales invalidas']);
    exit;
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['login_name'];

echo json_encode(['ok' => true, 'message' => 'Acceso exitoso']);
$conn->close();
?>
