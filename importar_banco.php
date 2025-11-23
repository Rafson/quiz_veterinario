<?php
echo "=== IMPORTANDO BANCO DE DADOS ===\n\n";

$host = 'localhost';
$user = 'root';
$pass = '!Mastim171819';

// Conectar ao MySQL
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error . "\n");
}

echo "✅ Conectado ao MySQL\n";

// Ler arquivo SQL
$sql = file_get_contents('database.sql');

if ($sql === false) {
    die("❌ Erro ao ler database.sql\n");
}

echo "✅ Arquivo database.sql lido\n";

// Executar os comandos SQL
$conn->multi_query($sql);

// Aguardar todas as queries terminarem
do {
    if ($result = $conn->store_result()) {
        $result->free();
    }
} while ($conn->more_results() && $conn->next_result());

if ($conn->error) {
    echo "❌ Erro ao executar SQL: " . $conn->error . "\n";
} else {
    echo "✅ SQL executado com sucesso!\n\n";
    
    // Verificar se as tabelas foram criadas
    $conn->select_db('QuizVeterinario');
    
    $tables = $conn->query("SHOW TABLES");
    echo "📋 Tabelas criadas:\n";
    while ($row = $tables->fetch_array()) {
        echo "   - " . $row[0] . "\n";
    }
    
    // Verificar questões
    $result = $conn->query("SELECT COUNT(*) as total FROM questoes");
    $total = $result->fetch_assoc()['total'];
    echo "\n✅ Total de questões inseridas: $total\n";
    
    echo "\n🎉 BANCO DE DADOS CONFIGURADO COM SUCESSO!\n";
    echo "Acesse: http://localhost:8000/index.php\n";
}

$conn->close();
?>
