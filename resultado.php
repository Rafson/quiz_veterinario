<?php
// Habilitar exibição de erros para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config.php';

// Verificar se o participante existe
if (!isset($_SESSION['participante_id'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();
$participante_id = $_SESSION['participante_id'];

// Buscar dados do participante
$stmt = $conn->prepare("SELECT * FROM participantes WHERE id = ?");
$stmt->bind_param("i", $participante_id);
$stmt->execute();
$participante = $stmt->get_result()->fetch_assoc();

if (!$participante) {
    header('Location: index.php');
    exit;
}

// Buscar ranking (top 10) - ordenado por pontuação DESC e tempo ASC
$sql_ranking = "SELECT nome_completo, pontuacao, tempo_segundos, data_realizacao FROM participantes ORDER BY pontuacao DESC, tempo_segundos ASC LIMIT 10";
$ranking = $conn->query($sql_ranking);

// Buscar respostas do participante com detalhes das questões
$sql_respostas = "
    SELECT 
        q.numero,
        q.pergunta,
        q.opcao_a,
        q.opcao_b,
        q.opcao_c,
        q.opcao_d,
        q.resposta_correta,
        r.resposta_escolhida,
        r.acertou
    FROM respostas r
    JOIN questoes q ON r.questao_id = q.id
    WHERE r.participante_id = ?
    ORDER BY q.numero
";
$stmt = $conn->prepare($sql_respostas);
$stmt->bind_param("i", $participante_id);
$stmt->execute();
$respostas = $stmt->get_result();

$questoes_certas = [];
$questoes_erradas = [];

while ($row = $respostas->fetch_assoc()) {
    if ($row['acertou']) {
        $questoes_certas[] = $row;
    } else {
        $questoes_erradas[] = $row;
    }
}

$conn->close();

// Função para obter a opção por letra
function getOpcaoTexto($questao, $letra) {
    $opcoes = [
        'A' => $questao['opcao_a'],
        'B' => $questao['opcao_b'],
        'C' => $questao['opcao_c'],
        'D' => $questao['opcao_d']
    ];
    return $opcoes[$letra] ?? '';
}

// Função para formatar tempo
function formatarTempo($segundos) {
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    $segs = $segundos % 60;
    
    if ($horas > 0) {
        return sprintf('%02d:%02d:%02d', $horas, $minutos, $segs);
    }
    return sprintf('%02d:%02d', $minutos, $segs);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Veterinária - Resultado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
        </div>
        <div class="result-header">
            <h1>🎉 Quiz Finalizado!</h1>
            <div class="participant-result">
                <h2><?php echo htmlspecialchars($participante['nome_completo']); ?></h2>
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score-number"><?php echo $participante['pontuacao']; ?></span>
                        <span class="score-total">/ 20</span>
                    </div>
                    <p class="score-percentage"><?php echo round(($participante['pontuacao'] / 20) * 100); ?>% de acertos</p>
                    <p class="score-time">⏱️ Tempo: <strong><?php echo formatarTempo($participante['tempo_segundos']); ?></strong></p>
                </div>
            </div>
        </div>
        
        <!-- Ranking -->
        <div class="ranking-section">
            <h2>🏆 Ranking dos Melhores</h2>
            <div class="ranking-table">
                <table>
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome</th>
                            <th>Pontuação</th>
                            <th>Tempo</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $posicao = 1;
                        while ($rank = $ranking->fetch_assoc()): 
                            $isCurrentUser = ($rank['nome_completo'] === $participante['nome_completo'] && 
                                            $rank['pontuacao'] === $participante['pontuacao'] &&
                                            $rank['tempo_segundos'] === $participante['tempo_segundos']);
                            $rowClass = $isCurrentUser ? 'current-user' : '';
                        ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td class="position">
                                    <?php 
                                    if ($posicao === 1) echo '🥇';
                                    elseif ($posicao === 2) echo '🥈';
                                    elseif ($posicao === 3) echo '🥉';
                                    else echo $posicao . 'º';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($rank['nome_completo']); ?></td>
                                <td class="score"><?php echo $rank['pontuacao']; ?>/20</td>
                                <td class="time">⏱️ <?php echo formatarTempo($rank['tempo_segundos']); ?></td>
                                <td class="date"><?php echo date('d/m/Y H:i:s', strtotime($rank['data_realizacao'])); ?></td>
                            </tr>
                        <?php 
                            $posicao++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Questões Acertadas -->
        <div class="answers-section">
            <h2 class="section-correct">✅ Questões Acertadas (<?php echo count($questoes_certas); ?>)</h2>
            <?php if (empty($questoes_certas)): ?>
                <p class="no-answers">Nenhuma questão acertada.</p>
            <?php else: ?>
                <?php foreach ($questoes_certas as $questao): ?>
                    <div class="answer-card correct">
                        <div class="answer-header">
                            <span class="question-number">Questão <?php echo $questao['numero']; ?></span>
                            <span class="badge badge-correct">Acertou</span>
                        </div>
                        <h4><?php echo htmlspecialchars($questao['pergunta']); ?></h4>
                        <p class="correct-answer">
                            <strong>Resposta correta:</strong> 
                            <?php echo $questao['resposta_correta']; ?>) 
                            <?php echo htmlspecialchars(getOpcaoTexto($questao, $questao['resposta_correta'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Questões Erradas -->
        <div class="answers-section">
            <h2 class="section-wrong">❌ Questões Erradas (<?php echo count($questoes_erradas); ?>)</h2>
            <?php if (empty($questoes_erradas)): ?>
                <p class="no-answers">Parabéns! Você acertou todas as questões! 🎊</p>
            <?php else: ?>
                <?php foreach ($questoes_erradas as $questao): ?>
                    <div class="answer-card wrong">
                        <div class="answer-header">
                            <span class="question-number">Questão <?php echo $questao['numero']; ?></span>
                            <span class="badge badge-wrong">Errou</span>
                        </div>
                        <h4><?php echo htmlspecialchars($questao['pergunta']); ?></h4>
                        <p class="your-answer">
                            <strong>Sua resposta:</strong> 
                            <?php if ($questao['resposta_escolhida']): ?>
                                <?php echo $questao['resposta_escolhida']; ?>) 
                                <?php echo htmlspecialchars(getOpcaoTexto($questao, $questao['resposta_escolhida'])); ?>
                            <?php else: ?>
                                Não respondida
                            <?php endif; ?>
                        </p>
                        <p class="correct-answer">
                            <strong>Resposta correta:</strong> 
                            <?php echo $questao['resposta_correta']; ?>) 
                            <?php echo htmlspecialchars(getOpcaoTexto($questao, $questao['resposta_correta'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Botão para reiniciar -->
        <div class="action-buttons">
            <a href="index.php?restart=1" class="btn-primary btn-large" onclick="return confirmRestart();">Fazer Novo Quiz</a>
        </div>
    </div>
    
    <script>
        function confirmRestart() {
            <?php 
            // Limpar sessão se clicou em reiniciar
            if (isset($_GET['restart'])) {
                echo "sessionStorage.clear();";
                session_destroy();
            }
            ?>
            return true;
        }
    </script>
</body>
</html>
<?php
// Limpar sessão após mostrar resultado
if (isset($_GET['restart'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
