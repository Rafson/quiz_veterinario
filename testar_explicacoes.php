<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$conn = getConnection();

// Verificar estrutura da tabela
echo "<h2>Estrutura da tabela questoes:</h2>";
$result = $conn->query("SHOW COLUMNS FROM questoes");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// Buscar questões 12 a 15 com explicações
echo "<h2>Questões 12-15 com explicações:</h2>";
$result = $conn->query("SELECT numero, pergunta, explicacao FROM questoes WHERE numero BETWEEN 12 AND 15 ORDER BY numero");

while ($row = $result->fetch_assoc()) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h3>Questão {$row['numero']}</h3>";
    echo "<p><strong>Pergunta:</strong> {$row['pergunta']}</p>";
    echo "<p><strong>Explicação:</strong> " . ($row['explicacao'] ? $row['explicacao'] : "<em style='color: red;'>NULL/VAZIO</em>") . "</p>";
    echo "</div>";
}

$conn->close();
?>
