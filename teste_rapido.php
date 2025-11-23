<?php
$conn = @new mysqli('localhost', 'root', '!Mastim171819');
if ($conn->connect_error) {
    echo "ERRO: " . $conn->connect_error . "\n";
} else {
    echo "✅ CONEXÃO COM MYSQL OK!\n";
    $result = $conn->query("SHOW DATABASES LIKE 'QuizVeterinario'");
    if ($result && $result->num_rows > 0) {
        echo "✅ BANCO 'QuizVeterinario' EXISTE!\n";
    } else {
        echo "⚠️  BANCO 'QuizVeterinario' NÃO EXISTE - Execute database.sql\n";
    }
    $conn->close();
}
?>
