<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajeria - Chat</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="chat-page">
        <aside class="sidebar">
            <h2>InstaMessage</h2>
            <p>Usuario: <strong id="my-username"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

            <label for="receiver">Enviar a:</label>
            <input type="text" id="receiver" placeholder="Escribe usuario existente">
            <button id="select-user">Seleccionar chat</button>

            <h3>Usuarios registrados</h3>
            <ul id="users-list"></ul>

            <a class="logout" href="api/logout.php">Cerrar sesion</a>
        </aside>

        <section class="chat-panel">
            <header>
                <h3 id="chat-title">Selecciona un usuario para chatear</h3>
            </header>

            <div id="messages" class="messages"></div>

            <form id="send-form" class="send-form">
                <input type="text" id="text" placeholder="Escribe un mensaje" required>
                <button type="submit">Enviar</button>
            </form>
            <p id="chat-message" class="message"></p>
        </section>
    </main>

    <script src="js/chat.js"></script>
</body>
</html>
