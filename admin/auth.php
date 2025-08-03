<?php
// admin/auth.php
session_start();

// Defina seu usuário e senha aqui. Em um sistema real, use hashing e banco de dados.
define('ADMIN_USERNAME', 'admin'); // Mude isso!
define('ADMIN_PASSWORD', 'senhaSuperSecreta123'); // Mude isso!

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Se não estiver logado, redireciona para a página de login
    // Verifica se já não está na página de login para evitar loop
    if (basename($_SERVER['PHP_SELF']) !== 'admin_login.php') {
        header('Location: admin_login.php');
        exit;
    }
}

// Função para verificar se é a página de login
function is_login_page() {
    return basename($_SERVER['PHP_SELF']) === 'admin_login.php';
}
?>
