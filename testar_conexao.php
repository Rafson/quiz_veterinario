<?php
echo "<h2>Teste de Conexão MySQL</h2>";

$host = 'localhost';
$user = 'root';
$pass = '!Mastim171819';
$db = 'QuizVeterinario';

echo "<p><strong>Testando conexão...</strong></p>";

// Testar conexão
$conn = @new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Erro ao conectar ao MySQL: " . $conn->connect_error . "</p>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Verifique se a senha está correta</li>";
    echo "<li>Verifique se o usuário 'root' tem permissão</li>";
    echo "</ul>";
    exit;
}

echo "<p style='color: green;'>✅ Conexão com MySQL estabelecida!</p>";

// Verificar se o banco existe
$result = $conn->query("SHOW DATABASES LIKE 'QuizVeterinario'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ Banco de dados 'QuizVeterinario' não existe ainda.</p>";
    echo "<p><strong>Execute o arquivo database.sql para criar o banco!</strong></p>";
} else {
    echo "<p style='color: green;'>✅ Banco de dados 'QuizVeterinario' existe!</p>";
    
    // Conectar ao banco
    $conn->select_db($db);
    
    // Verificar tabelas
    $tables = $conn->query("SHOW TABLES");
    if ($tables->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabelas encontradas:</p>";
        echo "<ul>";
        while ($row = $tables->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        // Verificar questões
        $questoes = $conn->query("SELECT COUNT(*) as total FROM questoes");
        $total = $questoes->fetch_assoc()['total'];
        echo "<p style='color: green;'>✅ Total de questões cadastradas: <strong>$total</strong></p>";
        
        if ($total == 20) {
            echo "<p style='color: green; font-size: 18px;'><strong>🎉 Tudo está configurado corretamente!</strong></p>";
            echo "<p><a href='index.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Começar Quiz</a></p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhuma tabela encontrada no banco.</p>";
        echo "<p><strong>Execute o arquivo database.sql para criar as tabelas!</strong></p>";
    }
}

$conn->close();
?>
