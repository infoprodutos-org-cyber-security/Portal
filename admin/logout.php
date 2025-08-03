<?php
// admin/logout.php
session_start();
$_SESSION = array(); // Limpa todas as variáveis de sessão
if (ini_get("session.use_cookies")) { // Remove o cookie de sessão
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy(); // Destrói a sessão
header('Location: admin_login.php'); // Redireciona para a página de login
exit;
?>
