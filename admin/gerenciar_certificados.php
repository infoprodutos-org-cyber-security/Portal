<?php
// admin/gerenciar_certificados.php
require_once 'auth.php'; // Garante que o admin está logado

$mensagem_cert = '';
$sucesso_cert = false;

// Caminho para o arquivo de certificados validados
$arquivo_certificados_validados = __DIR__ . '/../data_site/certificados_validados.json';

// Garante que a pasta data_site exista
if (!file_exists(dirname($arquivo_certificados_validados))) {
    mkdir(dirname($arquivo_certificados_validados), 0755, true);
}

// Carrega os certificados existentes
$certificados_validados = [];
if (file_exists($arquivo_certificados_validados)) {
    $conteudo_json = file_get_contents($arquivo_certificados_validados);
    $certificados_validados = json_decode($conteudo_json, true);
    if ($certificados_validados === null) { // Erro ao decodificar JSON
        $certificados_validados = [];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['validar_certificado']) && !empty($_POST['num_validar'])) {
        $num_validar = trim($_POST['num_validar']);
        if (ctype_digit($num_validar)) {
            if (!in_array($num_validar, $certificados_validados)) {
                $certificados_validados[] = $num_validar;
                // Remover duplicados, caso haja por algum erro anterior
                $certificados_validados = array_unique($certificados_validados); 
                if (file_put_contents($arquivo_certificados_validados, json_encode(array_values($certificados_validados), JSON_PRETTY_PRINT))) {
                    $mensagem_cert = "Certificado número " . htmlspecialchars($num_validar) . " VALIDADO com sucesso!";
                    $sucesso_cert = true;
                } else {
                    $mensagem_cert = "Erro ao salvar o arquivo de certificados.";
                }
            } else {
                $mensagem_cert = "Certificado número " . htmlspecialchars($num_validar) . " já está validado.";
            }
        } else {
            $mensagem_cert = "Número de certificado para validar deve conter apenas dígitos.";
        }
    } elseif (isset($_POST['invalidar_certificado']) && !empty($_POST['num_invalidar'])) {
        $num_invalidar = trim($_POST['num_invalidar']);
         if (ctype_digit($num_invalidar)) {
            if (($key = array_search($num_invalidar, $certificados_validados)) !== false) {
                unset($certificados_validados[$key]);
                if (file_put_contents($arquivo_certificados_validados, json_encode(array_values($certificados_validados), JSON_PRETTY_PRINT))) {
                    $mensagem_cert = "Certificado número " . htmlspecialchars($num_invalidar) . " INVALIDADO com sucesso!";
                    $sucesso_cert = true;
                } else {
                    $mensagem_cert = "Erro ao salvar o arquivo de certificados.";
                }
            } else {
                $mensagem_cert = "Certificado número " . htmlspecialchars($num_invalidar) . " não encontrado para invalidar.";
            }
        } else {
            $mensagem_cert = "Número de certificado para invalidar deve conter apenas dígitos.";
        }
    }
}
// Recarrega os certificados após modificação para exibição
if (file_exists($arquivo_certificados_validados)) {
    $conteudo_json_atualizado = file_get_contents($arquivo_certificados_validados);
    $certificados_validados_exibicao = json_decode($conteudo_json_atualizado, true) ?: [];
} else {
    $certificados_validados_exibicao = [];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Certificados - INFOPRODUTOS</title>
    <link rel="stylesheet" href="../static/style.css">
    <link rel="shortcut icon" href="../static/logoPK.png" type="image/x-icon" />
</head>
<body class="admin-page">
    <header class="admin-header">
        <div class="interface">
            <img src="../static/logoPK.png" alt="Logo Infoprodutos" class="admin-logo-header">
            <h1>Gerenciar Certificados</h1>
            <nav>
                <a href="painel_admin.php">Início</a>
                <a href="gerenciar_certificados.php">Gerenciar Certificados</a>
                <a href="gerenciar_ranking.php">Gerenciar Ranking</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="admin-main interface">
        <section class="manage-section">
            <h2>Validar Novo Certificado</h2>
            <?php if (!empty($mensagem_cert)): ?>
                <p class="message <?php echo $sucesso_cert ? 'success' : 'error'; ?>"><?php echo $mensagem_cert; ?></p>
            <?php endif; ?>

            <form method="POST" action="gerenciar_certificados.php" class="admin-form">
                <label for="num_validar">Número do Certificado para VALIDAR:</label>
                <input type="text" id="num_validar" name="num_validar" required pattern="\d+">
                <button type="submit" name="validar_certificado" class="btn-action">Validar</button>
            </form>
        </section>
        <hr class="admin-divider">
        <section class="manage-section">
            <h2>Invalidar Certificado Existente</h2>
            <form method="POST" action="gerenciar_certificados.php" class="admin-form">
                <label for="num_invalidar">Número do Certificado para INVALIDAR:</label>
                <input type="text" id="num_invalidar" name="num_invalidar" required pattern="\d+">
                <button type="submit" name="invalidar_certificado" class="btn-action btn-danger">Invalidar</button>
            </form>
        </section>
        <hr class="admin-divider">
        <section class="current-data-section">
            <h2>Certificados Atualmente Validados:</h2>
            <div class="data-display">
                <?php if (!empty($certificados_validados_exibicao)): ?>
                    <ul>
                        <?php foreach ($certificados_validados_exibicao as $cert): ?>
                            <li><?php echo htmlspecialchars($cert); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Nenhum certificado validado no momento.</p>
                <?php endif; ?>
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
