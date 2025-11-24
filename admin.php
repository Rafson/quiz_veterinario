<?php
// Habilitar exibição de erros para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config.php';

// Senha de administração (você pode mudar isso)
define('ADMIN_PASSWORD', 'admin123');

// Verificar login
$logged_in = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $logged_in = true;
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $senha = $_POST['senha'] ?? '';
    if ($senha === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $logged_in = true;
        $mensagem_sucesso = "Login realizado com sucesso!";
    } else {
        $erro = "Senha incorreta!";
    }
}

// Processar logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    session_start();
    header('Location: admin.php');
    exit;
}

// Processar ações administrativas
if ($logged_in) {
    $conn = getConnection();
    $total_questoes = getTotalQuestoes();
    
    // Apagar todos os registros
    if (isset($_POST['limpar_ranking'])) {
        $conn->query("DELETE FROM respostas");
        $conn->query("DELETE FROM participantes");
        $conn->query("ALTER TABLE participantes AUTO_INCREMENT = 1");
        $mensagem_sucesso = "✅ Todos os rankings foram apagados com sucesso!";
    }
    
    // Exportar para CSV
    if (isset($_GET['exportar_csv'])) {
        $sql = "SELECT 
                    p.id,
                    p.nome_completo,
                    p.pontuacao,
                    p.tempo_segundos,
                    p.data_realizacao
                FROM participantes p
                ORDER BY p.pontuacao DESC, p.tempo_segundos ASC";
        
        $result = $conn->query($sql);
        
        // Configurar headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=ranking_quiz_' . date('Y-m-d_H-i-s') . '.csv');
        
        // Abrir output
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho do CSV
        fputcsv($output, ['ID', 'Nome Completo', 'Pontuação', 'Tempo (segundos)', 'Tempo Formatado', 'Data e Hora'], ';');
        
        // Dados
        while ($row = $result->fetch_assoc()) {
            $tempo_formatado = formatarTempo($row['tempo_segundos']);
            fputcsv($output, [
                $row['id'],
                $row['nome_completo'],
                $row['pontuacao'] . '/' . getTotalQuestoes(),
                $row['tempo_segundos'],
                $tempo_formatado,
                date('d/m/Y H:i:s', strtotime($row['data_realizacao']))
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    // Buscar estatísticas
    $stats = [
        'total_participantes' => 0,
        'media_pontuacao' => 0,
        'media_tempo' => 0,
        'melhor_pontuacao' => 0,
        'pior_pontuacao' => 0
    ];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM participantes");
    $stats['total_participantes'] = $result->fetch_assoc()['total'];
    
    if ($stats['total_participantes'] > 0) {
        $result = $conn->query("SELECT AVG(pontuacao) as media FROM participantes");
        $stats['media_pontuacao'] = round($result->fetch_assoc()['media'], 2);
        
        $result = $conn->query("SELECT AVG(tempo_segundos) as media FROM participantes");
        $stats['media_tempo'] = round($result->fetch_assoc()['media'], 0);
        
        $result = $conn->query("SELECT MAX(pontuacao) as max FROM participantes");
        $stats['melhor_pontuacao'] = $result->fetch_assoc()['max'];
        
        $result = $conn->query("SELECT MIN(pontuacao) as min FROM participantes");
        $stats['pior_pontuacao'] = $result->fetch_assoc()['min'];
    }
    
    // Buscar ranking completo
    $sql_ranking = "SELECT nome_completo, pontuacao, tempo_segundos, data_realizacao FROM participantes ORDER BY pontuacao DESC, tempo_segundos ASC";
    $ranking = $conn->query($sql_ranking);
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
    <title>Administração - Quiz Veterinária</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 0.95em;
            text-transform: uppercase;
        }
        
        .stat-card .value {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
        }
        
        .admin-actions {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        
        .admin-actions h2 {
            color: #667eea;
            margin-top: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1em;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 67, 54, 0.6);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1em;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.6);
        }
        
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .logout-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .mensagem-sucesso {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
        </div>
    <?php if (!$logged_in): ?>
        <!-- Tela de Login -->
        <div class="login-box">
            <h1 style="text-align: center; color: #667eea;">🔐 Administração</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">Digite a senha de administrador</p>
            
            <?php if (isset($erro)): ?>
                <div class="error-message"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        placeholder="Digite a senha" 
                        required
                        autofocus
                    >
                </div>
                
                <button type="submit" name="login" class="btn-primary" style="width: 100%;">Entrar</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: #667eea;">← Voltar ao Quiz</a>
            </p>
        </div>
    <?php else: ?>
        <!-- Painel Administrativo -->
        <div class="admin-container">
            <div class="admin-header">
                <h1>⚙️ Painel Administrativo</h1>
                <p>Quiz de Veterinária</p>
                <a href="admin.php?logout=1" class="logout-btn">Sair</a>
            </div>
            
            <?php if (isset($mensagem_sucesso)): ?>
                <div class="mensagem-sucesso"><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
            <?php endif; ?>
            
            <!-- Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>👥 Total de Participantes</h3>
                    <div class="value"><?php echo $stats['total_participantes']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>📊 Média de Pontuação</h3>
                    <div class="value"><?php echo $stats['media_pontuacao']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>⏱️ Tempo Médio</h3>
                    <div class="value" style="font-size: 1.8em;"><?php echo formatarTempo($stats['media_tempo']); ?></div>
                </div>
                <div class="stat-card">
                    <h3>🏆 Melhor Pontuação</h3>
                    <div class="value"><?php echo $stats['melhor_pontuacao']; ?>/<?php echo $total_questoes; ?></div>
                </div>
                <div class="stat-card">
                    <h3>📉 Menor Pontuação</h3>
                    <div class="value"><?php echo $stats['pior_pontuacao']; ?>/<?php echo $total_questoes; ?></div>
                </div>
            </div>
            
            <!-- Ações Administrativas -->
            <div class="admin-actions">
                <h2>🛠️ Ações Administrativas</h2>
                <div class="action-buttons">
                    <a href="admin.php?exportar_csv=1" class="btn-success">
                        📥 Exportar Ranking (CSV)
                    </a>
                    
                    <form method="POST" action="" style="display: inline;" onsubmit="return confirm('⚠️ ATENÇÃO! Esta ação irá apagar TODOS os registros do ranking permanentemente. Deseja continuar?');">
                        <button type="submit" name="limpar_ranking" class="btn-danger">
                            🗑️ Limpar Todo o Ranking
                        </button>
                    </form>
                    
                    <a href="index.php" class="btn-primary">
                        🏠 Voltar ao Quiz
                    </a>
                </div>
            </div>
            
            <!-- Ranking Completo -->
            <?php if ($ranking && $ranking->num_rows > 0): ?>
            <div class="ranking-section">
                <h2>🏆 Ranking Completo (<?php echo $ranking->num_rows; ?> participantes)</h2>
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
                                    <td class="score"><?php echo $rank['pontuacao']; ?>/<?php echo $total_questoes; ?></td>
                                    <td class="time"><?php echo formatarTempo($rank['tempo_segundos']); ?></td>
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
            <?php else: ?>
                <div class="admin-actions">
                    <p style="text-align: center; color: #999; font-size: 1.2em;">Nenhum participante registrado ainda.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php if (isset($conn)) $conn->close(); ?>
    <?php endif; ?>
</body>
</html>
