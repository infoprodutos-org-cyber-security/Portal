<?php
// admin/gerenciar_ranking.php
require_once 'auth.php'; // Garante que o admin está logado

$mensagem_rank = '';
$sucesso_rank = false;

// Caminho para o arquivo de ranking
$arquivo_ranking = __DIR__ . '/../data_site/ranking_alunos.json';

// Garante que a pasta data_site exista
if (!file_exists(dirname($arquivo_ranking))) {
    mkdir(dirname($arquivo_ranking), 0755, true);
}

// Carrega o ranking existente
$ranking_alunos = [];
if (file_exists($arquivo_ranking)) {
    $conteudo_json = file_get_contents($arquivo_ranking);
    $ranking_alunos = json_decode($conteudo_json, true);
    if ($ranking_alunos === null) { // Erro ao decodificar JSON
        $ranking_alunos = [];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adicionar ou Atualizar Aluno
    if (isset($_POST['salvar_aluno'])) {
        $nome_aluno = trim($_POST['nome_aluno'] ?? '');
        $pontuacao_aluno = filter_var(trim($_POST['pontuacao_aluno'] ?? ''), FILTER_VALIDATE_INT);
        $curso_aluno = trim($_POST['curso_aluno'] ?? ''); // Curso é opcional

        if (!empty($nome_aluno) && $pontuacao_aluno !== false && $pontuacao_aluno >= 0) {
            $aluno_existente_key = null;
            foreach ($ranking_alunos as $key => $aluno) {
                if (strtolower($aluno['nome']) === strtolower($nome_aluno)) {
                    $aluno_existente_key = $key;
                    break;
                }
            }

            $dados_aluno = ['nome' => $nome_aluno, 'pontuacao' => $pontuacao_aluno, 'curso' => $curso_aluno];

            if ($aluno_existente_key !== null) { // Atualiza aluno existente
                $ranking_alunos[$aluno_existente_key] = $dados_aluno;
                $mensagem_rank = "Aluno " . htmlspecialchars($nome_aluno) . " atualizado no ranking!";
            } else { // Adiciona novo aluno
                $ranking_alunos[] = $dados_aluno;
                $mensagem_rank = "Aluno " . htmlspecialchars($nome_aluno) . " adicionado ao ranking!";
            }
            $sucesso_rank = true;

        } else {
            $mensagem_rank = "Erro: Nome do aluno é obrigatório e pontuação deve ser um número válido (maior ou igual a zero).";
        }
    }
    // Remover Aluno
    elseif (isset($_POST['remover_aluno'])) {
        $nome_remover = trim($_POST['nome_remover'] ?? '');
        $aluno_encontrado_para_remover = false;
        foreach ($ranking_alunos as $key => $aluno) {
            if (strtolower($aluno['nome']) === strtolower($nome_remover)) {
                unset($ranking_alunos[$key]);
                $ranking_alunos = array_values($ranking_alunos); // Reindexa o array
                $mensagem_rank = "Aluno " . htmlspecialchars($nome_remover) . " removido do ranking.";
                $sucesso_rank = true;
                $aluno_encontrado_para_remover = true;
                break;
            }
        }
        if (!$aluno_encontrado_para_remover) {
            $mensagem_rank = "Aluno " . htmlspecialchars($nome_remover) . " não encontrado no ranking.";
        }
    }

    // Salva as alterações no arquivo JSON
    if ($sucesso_rank) { // Salva apenas se houve uma operação bem sucedida
        if (!file_put_contents($arquivo_ranking, json_encode($ranking_alunos, JSON_PRETTY_PRINT))) {
            $mensagem_rank .= " (Erro ao salvar o arquivo de ranking.)";
            $sucesso_rank = false; // Indica que o salvamento falhou
        }
    }
}
// Recarrega o ranking para exibição após modificação
if (file_exists($arquivo_ranking)) {
    $conteudo_json_atualizado_rank = file_get_contents($arquivo_ranking);
    $ranking_alunos_exibicao = json_decode($conteudo_json_atualizado_rank, true) ?: [];
} else {
    $ranking_alunos_exibicao = [];
}
// Ordenar para exibição
if (!empty($ranking_alunos_exibicao)) {
    usort($ranking_alunos_exibicao, function ($a, $b) {
        return $b['pontuacao'] - $a['pontuacao'];
    });
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Ranking de Alunos - INFOPRODUTOS</title>
    <link rel="stylesheet" href="../static/style.css">
    <link rel="shortcut icon" href="../static/logoPK.png" type="image/x-icon" />
</head>
<body class="admin-page">
    <header class="admin-header">
        <div class="interface">
            <img src="../static/logoPK.png" alt="Logo Infoprodutos" class="admin-logo-header">
            <h1>Gerenciar Ranking de Alunos</h1>
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
            <h2>Adicionar/Atualizar Aluno no Ranking</h2>
            <?php if (!empty($mensagem_rank)): ?>
                <p class="message <?php echo $sucesso_rank ? 'success' : 'error'; ?>"><?php echo $mensagem_rank; ?></p>
            <?php endif; ?>

            <form method="POST" action="gerenciar_ranking.php" class="admin-form">
                <div>
                    <label for="nome_aluno">Nome do Aluno:</label>
                    <input type="text" id="nome_aluno" name="nome_aluno" required>
                </div>
                <div>
                    <label for="pontuacao_aluno">Pontuação:</label>
                    <input type="number" id="pontuacao_aluno" name="pontuacao_aluno" required min="0">
                </div>
                <div>
                    <label for="curso_aluno">Curso Destaque (Opcional):</label>
                    <input type="text" id="curso_aluno" name="curso_aluno">
                </div>
                <button type="submit" name="salvar_aluno" class="btn-action">Salvar Aluno</button>
            </form>
        </section>
        <hr class="admin-divider">
        <section class="manage-section">
            <h2>Remover Aluno do Ranking</h2>
            <form method="POST" action="gerenciar_ranking.php" class="admin-form">
                <div>
                    <label for="nome_remover">Nome do Aluno para Remover:</label>
                    <input type="text" id="nome_remover" name="nome_remover" required>
                </div>
                <button type="submit" name="remover_aluno" class="btn-action btn-danger">Remover Aluno</button>
            </form>
        </section>
        <hr class="admin-divider">
        <section class="current-data-section">
            <h2>Ranking Atual de Alunos:</h2>
            <div class="data-display">
                <?php if (!empty($ranking_alunos_exibicao)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Pontuação</th>
                                <th>Curso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ranking_alunos_exibicao as $aluno): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($aluno['pontuacao']); ?></td>
                                    <td><?php echo isset($aluno['curso']) ? htmlspecialchars($aluno['curso']) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum aluno no ranking no momento.</p>
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
