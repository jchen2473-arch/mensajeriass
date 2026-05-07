<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connection.php';
mysqli_report(MYSQLI_REPORT_OFF);

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['ok' => false, 'message' => 'Completa todos los campos']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['ok' => false, 'message' => 'La contrasena debe tener al menos 6 caracteres']);
    exit;
}

$check = $conn->prepare('SELECT id FROM usuarios WHERE username = ? LIMIT 1');
$check->bind_param('s', $username);
$check->execute();
$exists = $check->get_result()->fetch_assoc();

if ($exists) {
    echo json_encode(['ok' => false, 'message' => 'El usuario ya existe']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$nombre = $username;
$email = $username . '@instamessage.local';
$stmt = $conn->prepare('INSERT INTO usuarios (usuario, username, nombre, email, password_hash) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $username, $username, $nombre, $email, $passwordHash);

if (!$stmt->execute()) {
    if ($conn->errno === 1062) {
        echo json_encode(['ok' => false, 'message' => 'El usuario ya existe']);
        exit;
    }
    echo json_encode(['ok' => false, 'message' => 'No se pudo crear la cuenta']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Cuenta creada, ya puedes iniciar sesion']);
$conn->close();
?>
