<?php
// Habilitar exibição de erros para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config.php';

// Se pediu para reiniciar, limpar sessão
if (isset($_GET['restart'])) {
    session_destroy();
    session_start();
    header('Location: index.php');
    exit;
}

// Se já tem nome na sessão e não está finalizando quiz, redirecionar
if (isset($_SESSION['nome_completo']) && !isset($_SESSION['participante_id'])) {
    header('Location: quiz.php');
    exit;
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_completo'] ?? '');
    
    if (empty($nome)) {
        $erro = "Por favor, digite seu nome completo.";
    } else {
        // Limpar qualquer sessão anterior
        session_destroy();
        session_start();
        
        $_SESSION['nome_completo'] = $nome;
        header('Location: quiz.php');
        exit;
    }
}

// Buscar ranking para exibir na página inicial
$conn = getConnection();
$sql_ranking = "SELECT nome_completo, pontuacao, tempo_segundos, data_realizacao FROM participantes ORDER BY pontuacao DESC, tempo_segundos ASC LIMIT 10";
$ranking = $conn->query($sql_ranking);

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
    <title>Quiz Veterinária - Início</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <div class="logo-container">
                <img src="images/logo.png" alt="Logo" class="logo">
            </div>
            
            <h1>🐾 Quiz de Veterinária</h1>
            <p class="subtitle">Teste seus conhecimentos com 25 perguntas sobre neurologia veterinária!</p>
            
            <?php if (isset($erro)): ?>
                <div class="error-message"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="index.php">
                <div class="form-group">
                    <label for="nome_completo">Nome Completo:</label>
                    <input 
                        type="text" 
                        id="nome_completo" 
                        name="nome_completo" 
                        placeholder="Digite seu nome completo" 
                        value="<?php echo isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : ''; ?>"
                        maxlength="50"
                        required
                        autofocus
                    >
                    <small style="color: #666; font-size: 0.85em;">Máximo 50 caracteres</small>
                </div>
                
                <button type="submit" class="btn-primary">Começar Quiz</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="admin.php" style="color: #667eea; font-size: 0.9em;">⚙️ Área Administrativa</a>
            </p>
        </div>
        
        <!-- Informações do Curso -->
        <div class="course-info-box">
            <h2>A3 – Quiz "Estrutura e Sintomas Neurológicos em Cães"</h2>
            
            <p><strong>UC Sistema Nervoso e Aparelho Locomotor dos Animais</strong></p>
            <p>Prof. Draª Maria Letícia Baptista Salvadori</p>
            <p>Prof. Dr. Paulo Ramos</p>
            <br>
            <p><strong>Curso Medicina Veterinária / Unidade Butantã / Nov/2025</strong></p>
            <p>Ana Maria Siqueira - RA 825166704</p>
            <p>Camila Fernandes Passos - RA 825127608</p>
            <p>Fernanda Mendes Fernandes - RA 825153627</p>
            <p>Emilly Feliciano de Moura - RA 82527267</p>
        </div>
        
        <!-- Ranking na página inicial -->
        <?php if ($ranking && $ranking->num_rows > 0): ?>
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
                        ?>
                            <tr>
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
        <?php endif; ?>
    </div>
<?php $conn->close(); ?>
</body>
</html>
