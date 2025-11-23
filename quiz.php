<?php
// Habilitar exibição de erros para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config.php';

// Verificar se o usuário informou o nome
if (!isset($_SESSION['nome_completo'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

// Buscar todas as questões
$sql = "SELECT * FROM questoes ORDER BY numero";
$result = $conn->query($sql);
$questoes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questoes[] = $row;
    }
}

// Processar respostas do quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respostas'])) {
    $nome = $_SESSION['nome_completo'];
    $respostas = $_POST['respostas'];
    $tempo_segundos = intval($_POST['tempo_segundos'] ?? 0);
    $pontuacao = 0;
    
    // Inserir participante com tempo
    $stmt = $conn->prepare("INSERT INTO participantes (nome_completo, pontuacao, tempo_segundos) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $nome, $pontuacao, $tempo_segundos);
    $stmt->execute();
    $participante_id = $conn->insert_id;
    
    // Processar cada resposta
    foreach ($questoes as $questao) {
        $questao_id = $questao['id'];
        $resposta_escolhida = $respostas[$questao_id] ?? '';
        $acertou = ($resposta_escolhida === $questao['resposta_correta']) ? 1 : 0;
        
        if ($acertou) {
            $pontuacao++;
        }
        
        // Inserir resposta
        $stmt = $conn->prepare("INSERT INTO respostas (participante_id, questao_id, resposta_escolhida, acertou) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $participante_id, $questao_id, $resposta_escolhida, $acertou);
        $stmt->execute();
    }
    
    // Atualizar pontuação
    $stmt = $conn->prepare("UPDATE participantes SET pontuacao = ? WHERE id = ?");
    $stmt->bind_param("ii", $pontuacao, $participante_id);
    $stmt->execute();
    
    // Salvar ID do participante na sessão
    $_SESSION['participante_id'] = $participante_id;
    
    // Redirecionar para resultados
    header('Location: resultado.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Veterinária - Questões</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
        </div>
        <div class="quiz-header">
            <h1>🐾 Quiz Veterinário</h1>
            <p class="participant-name">Participante: <strong><?php echo htmlspecialchars($_SESSION['nome_completo']); ?></strong></p>
            <div class="timer-container">
                <div class="timer-icon">⏱️</div>
                <div class="timer-display">
                    <div class="timer-label">Tempo</div>
                    <div class="timer-value" id="timer">00:00:00</div>
                </div>
            </div>
            <p class="quiz-info">20 perguntas • Marque a alternativa correta</p>
        </div>
        
        <form method="POST" action="" id="quizForm">
            <input type="hidden" name="tempo_segundos" id="tempo_segundos" value="0">
            <?php foreach ($questoes as $index => $questao): ?>
                <div class="question-card">
                    <div class="question-header">
                        <span class="question-number">Questão <?php echo $questao['numero']; ?></span>
                    </div>
                    
                    <h3 class="question-text"><?php echo htmlspecialchars($questao['pergunta']); ?></h3>
                    
                    <?php if (!empty($questao['explicacao'])): ?>
                        <blockquote class="question-explanation">
                            "<?php echo htmlspecialchars($questao['explicacao']); ?>"
                        </blockquote>
                    <?php endif; ?>
                    
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="respostas[<?php echo $questao['id']; ?>]" value="A" required>
                            <span class="option-letter">A)</span>
                            <span class="option-text"><?php echo htmlspecialchars($questao['opcao_a']); ?></span>
                        </label>
                        
                        <label class="option">
                            <input type="radio" name="respostas[<?php echo $questao['id']; ?>]" value="B" required>
                            <span class="option-letter">B)</span>
                            <span class="option-text"><?php echo htmlspecialchars($questao['opcao_b']); ?></span>
                        </label>
                        
                        <label class="option">
                            <input type="radio" name="respostas[<?php echo $questao['id']; ?>]" value="C" required>
                            <span class="option-letter">C)</span>
                            <span class="option-text"><?php echo htmlspecialchars($questao['opcao_c']); ?></span>
                        </label>
                        
                        <label class="option">
                            <input type="radio" name="respostas[<?php echo $questao['id']; ?>]" value="D" required>
                            <span class="option-letter">D)</span>
                            <span class="option-text"><?php echo htmlspecialchars($questao['opcao_d']); ?></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="submit-section">
                <button type="submit" class="btn-primary btn-large">Finalizar Quiz</button>
            </div>
        </form>
    </div>
    
    <script>
        // Cronômetro com persistência em sessionStorage
        const STORAGE_KEY = 'quiz_start_time';
        
        // Recuperar ou criar tempo inicial
        let startTime = sessionStorage.getItem(STORAGE_KEY);
        if (!startTime) {
            startTime = Date.now();
            sessionStorage.setItem(STORAGE_KEY, startTime);
        } else {
            startTime = parseInt(startTime);
        }
        
        let timerInterval;
        
        function updateTimer() {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            const display = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
            
            document.getElementById('timer').textContent = display;
            document.getElementById('tempo_segundos').value = elapsed;
        }
        
        // Iniciar cronômetro
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
        
        // Validação do formulário
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            clearInterval(timerInterval); // Parar cronômetro ao enviar
            // Limpar timer do sessionStorage ao finalizar
            sessionStorage.removeItem(STORAGE_KEY);
            
            const questionsCount = <?php echo count($questoes); ?>;
            let answeredCount = 0;
            
            for (let i = 1; i <= questionsCount; i++) {
                const questionInputs = document.querySelectorAll('input[type="radio"]:checked');
                answeredCount = questionInputs.length;
            }
            
            if (answeredCount < questionsCount) {
                if (!confirm('Você não respondeu todas as questões. Deseja continuar mesmo assim?')) {
                    e.preventDefault();
                    timerInterval = setInterval(updateTimer, 1000); // Retomar cronômetro
                }
            }
        });
    </script>
</body>
</html>
