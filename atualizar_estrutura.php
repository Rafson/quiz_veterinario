<?php
echo "=== ATUALIZANDO BANCO DE DADOS ===\n\n";

$conn = new mysqli('localhost', 'root', '!Mastim171819', 'QuizVeterinario');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error . "\n");
}

echo "✅ Conectado ao MySQL\n";

// Verificar se a coluna já existe
$result = $conn->query("SHOW COLUMNS FROM participantes LIKE 'tempo_segundos'");

if ($result->num_rows > 0) {
    echo "⚠️ Coluna tempo_segundos já existe\n";
} else {
    echo "📝 Adicionando coluna tempo_segundos...\n";
    $conn->query("ALTER TABLE participantes ADD COLUMN tempo_segundos INT NOT NULL DEFAULT 0 AFTER pontuacao");
    echo "✅ Coluna tempo_segundos adicionada\n";
}

// Atualizar índice
echo "📝 Atualizando índices...\n";
// Remover índice antigo se existir
$result = $conn->query("SHOW INDEX FROM participantes WHERE Key_name = 'idx_pontuacao'");
if ($result->num_rows > 0) {
    $conn->query("ALTER TABLE participantes DROP INDEX idx_pontuacao");
}
// Criar novo índice se não existir
$result = $conn->query("SHOW INDEX FROM participantes WHERE Key_name = 'idx_ranking'");
if ($result->num_rows == 0) {
    $conn->query("CREATE INDEX idx_ranking ON participantes(pontuacao DESC, tempo_segundos ASC)");
}
echo "✅ Índices atualizados\n";

// Mostrar estrutura da tabela
echo "\n📋 Estrutura da tabela participantes:\n";
$result = $conn->query("SHOW COLUMNS FROM participantes");
while ($row = $result->fetch_assoc()) {
    echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n🎉 Banco de dados atualizado com sucesso!\n";

$conn->close();
?>
