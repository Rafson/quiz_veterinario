# Quiz Veterinário 🐾

Sistema de quiz interativo sobre veterinária desenvolvido em PHP com MySQL.

## 📋 Características

- 20 questões sobre temas veterinários (cinomose, raiva, encefalite, lesões medulares, vacinação)
- Sistema de cadastro com nome completo do participante
- Ranking dos melhores participantes (Top 10)
- Relatório detalhado mostrando questões acertadas e erradas
- Interface responsiva e amigável
- Design moderno com gradientes e animações

## 🚀 Instalação

### 1. Pré-requisitos
- Servidor Apache com PHP 7.4+ (XAMPP, WAMP, LAMP, etc.)
- MySQL 5.7+

### 2. Configuração do Banco de Dados

1. Acesse o MySQL (via phpMyAdmin ou linha de comando)
2. Execute o arquivo `database.sql` para criar o banco de dados e as tabelas:

```bash
mysql -u root -p < database.sql
```

Ou importe via phpMyAdmin.

### 3. Configuração do Projeto

As credenciais do banco de dados já estão configuradas em `config.php`:
- Servidor: localhost
- Usuário: root
- Senha: !Mastim171819
- Banco: QuizVeterinario

### 4. Executar o Projeto

1. Coloque todos os arquivos na pasta do seu servidor web (htdocs, www, etc.)
2. Acesse pelo navegador: `http://localhost/Quiz_Veterinario`

## 📁 Estrutura de Arquivos

```
Quiz_Veterinario/
│
├── index.php          # Página inicial (cadastro do participante)
├── quiz.php           # Página com as 20 questões
├── resultado.php      # Página de resultados com ranking
├── config.php         # Configurações do banco de dados
├── style.css          # Estilização das páginas
├── database.sql       # Script de criação do banco
└── README.md          # Este arquivo
```

## 🎮 Como Usar

1. **Início**: Digite seu nome completo na página inicial
2. **Quiz**: Responda as 20 questões marcando a alternativa correta
3. **Resultado**: Veja sua pontuação, posição no ranking e revisão das respostas

## 🗄️ Estrutura do Banco de Dados

### Tabela `questoes`
- id (INT, PK)
- numero (INT)
- pergunta (TEXT)
- opcao_a, opcao_b, opcao_c, opcao_d (VARCHAR)
- resposta_correta (CHAR)

### Tabela `participantes`
- id (INT, PK)
- nome_completo (VARCHAR)
- pontuacao (INT)
- data_realizacao (TIMESTAMP)

### Tabela `respostas`
- id (INT, PK)
- participante_id (INT, FK)
- questao_id (INT, FK)
- resposta_escolhida (CHAR)
- acertou (BOOLEAN)

## 🎨 Funcionalidades

✅ Cadastro de participante  
✅ 20 questões de múltipla escolha  
✅ Validação de respostas  
✅ Cálculo automático de pontuação  
✅ Ranking dos melhores participantes  
✅ Revisão de questões acertadas e erradas  
✅ Design responsivo (mobile-friendly)  
✅ Interface intuitiva com feedback visual  

## 🔧 Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Estilo**: CSS personalizado com gradientes e animações

## 📝 Observações

- As respostas são armazenadas no banco de dados para criar o ranking
- O ranking exibe os top 10 participantes ordenados por pontuação
- Cada participação é registrada com data e hora
- O sistema destaca o usuário atual no ranking

## 👨‍💻 Desenvolvido para

Naara - Quiz Educacional de Veterinária

---

Desenvolvido com ❤️ usando PHP e MySQL
