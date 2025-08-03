<?php
// admin/painel_admin.php
require_once 'auth.php'; // Garante que o admin está logado
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - INFOPRODUTOS</title>
    <link rel="stylesheet" href="../static/style.css">
    <link rel="shortcut icon" href="../static/logoPK.png" type="image/x-icon" />
</head>
<body class="admin-page">
    <header class="admin-header">
        <div class="interface">
            <img src="../static/logoPK.png" alt="Logo Infoprodutos" class="admin-logo-header">
            <h1>Painel de Administração</h1>
            <nav>
                <a href="painel_admin.php">Início</a>
                <a href="gerenciar_certificados.php">Gerenciar Certificados</a>
                <a href="gerenciar_ranking.php">Gerenciar Ranking</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="admin-main interface">
        <section class="admin-welcome">
            <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
            <p>Utilize o menu acima para gerenciar os certificados e o ranking de alunos.</p>
        </section>

        <section class="admin-quick-actions">
            <h3>Ações Rápidas:</h3>
            <div class="actions-container">
                <div class="action-card">
                    <h4>Certificados</h4>
                    <p>Valide ou invalide números de certificados de conclusão.</p>
                    <a href="gerenciar_certificados.php" class="btn-secondary">Gerenciar Certificados</a>
                </div>
                <div class="action-card">
                    <h4>Ranking de Alunos</h4>
                    <p>Adicione, edite ou remova alunos do Hall da Fama.</p>
                    <a href="gerenciar_ranking.php" class="btn-secondary">Gerenciar Ranking</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="admin-footer">
        <div class="interface">
            <p>&copy; <?php echo date("Y"); ?> INFOPRODUTOS - Painel Administrativo</p>
        </div>
    </footer>
</body>
</html>
