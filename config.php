<?php
// Habilitar exibição de erros para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '!Mastim171819');
define('DB_NAME', 'QuizVeterinario');

// Criar conexão com o banco de dados
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Função para obter total de questões
function getTotalQuestoes() {
    static $total = null;
    if ($total === null) {
        $conn = getConnection();
        $result = $conn->query("SELECT COUNT(*) as total FROM questoes");
        $row = $result->fetch_assoc();
        $total = (int)$row['total'];
    }
    return $total;
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
