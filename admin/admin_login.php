<?php
// admin/admin_login.php
session_start(); // Inicia a sessão aqui também para poder definir a variável de sessão

// Se já estiver logado, redireciona para o painel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: painel_admin.php');
    exit;
}

// Definições de usuário e senha (devem ser as mesmas que em auth.php)
define('ADMIN_USERNAME_LOGIN', 'admin'); // Mude isso!
define('ADMIN_PASSWORD_LOGIN', 'senhaSuperSecreta123'); // Mude isso!

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME_LOGIN && $password === ADMIN_PASSWORD_LOGIN) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: painel_admin.php');
        exit;
    } else {
        $error_message = "Usuário ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - INFOPRODUTOS</title>
    <link rel="stylesheet" href="../static/style.css"> <link rel="shortcut icon" href="../static/logoPK.png" type="image/x-icon" />
</head>
<body class="admin-login-page">
    <div class="login-container interface">
        <img src="../static/logoPK.png" alt="Logo Infoprodutos" class="admin-logo">
        <h1>Acesso Restrito</h1>
        <p>Painel de Administração INFOPRODUTOS</p>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <div>
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-principal">Entrar</button>
        </form>
        <p class="back-to-site"><a href="../index.php">Voltar ao site</a></p>
    </div>
</body>
</html>
