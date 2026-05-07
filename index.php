<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajeria - Acceso</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <h1 class="brand">InstaMessage</h1>
            <p class="subtitle">Inicia sesion o crea tu cuenta</p>

            <div class="tabs">
                <button type="button" class="tab active" data-target="login-form">Iniciar sesion</button>
                <button type="button" class="tab" data-target="register-form">Registrar</button>
                <button type="button" class="tab" data-target="reset-form">Recuperar contraseña</button>
            </div>

            <form id="login-form" class="form active">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contrasena" required>
                <button type="submit">Entrar</button>
            </form>

            <form id="register-form" class="form">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contrasena" minlength="6" required>
                <button type="submit">Crear cuenta</button>
            </form>

            <form id="reset-form" class="form">
                <input type="text" name="username" placeholder="Usuario o correo" required>
                <button type="submit">Solicitar restablecimiento</button>
            </form>

            <p id="auth-message" class="message"></p>
        </section>
    </main>

    <script src="js/auth.js"></script>
</body>
</html>
