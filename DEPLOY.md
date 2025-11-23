# 🚀 GUIA DE DEPLOY - Quiz Veterinário

## 📋 Requisitos do Servidor

- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior
- **Extensões PHP necessárias**:
  - mysqli
  - session
  - mbstring

---

## 📁 Arquivos Necessários

### **Arquivos PHP (obrigatórios):**
- `index.php` - Página inicial
- `quiz.php` - Página do quiz
- `resultado.php` - Página de resultados
- `admin.php` - Painel administrativo
- `config.php` - Configurações do banco de dados

### **Arquivos de Estilo:**
- `style.css` - Estilos CSS

### **Arquivos de Banco de Dados:**
- `database_completo.sql` - Script completo para criar banco

### **Arquivos Opcionais (para desenvolvimento local):**
- `database.sql` - Script original
- `testar_conexao.php` - Teste de conexão
- `teste_rapido.php` - Teste rápido
- `importar_banco.php` - Importador de dados
- `atualizar_estrutura.php` - Atualizador de estrutura
- `atualizar_banco.sql` - Scripts de atualização
- `habilitar_mysqli.bat` - Script Windows
- `README.md` - Documentação

---

## 🔧 PASSO A PASSO DO DEPLOY

### **1️⃣ Preparar o Banco de Dados**

#### Opção A: Via phpMyAdmin
1. Acesse o phpMyAdmin do seu servidor
2. Clique em "Novo" para criar um banco de dados
3. Nome: `QuizVeterinario`
4. Cotejamento: `utf8mb4_unicode_ci`
5. Clique em "Importar"
6. Selecione o arquivo `database_completo.sql`
7. Clique em "Executar"

#### Opção B: Via Terminal/SSH
```bash
mysql -u SEU_USUARIO -p < database_completo.sql
```

#### Opção C: Via linha de comando MySQL
```sql
mysql -u SEU_USUARIO -p
CREATE DATABASE QuizVeterinario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QuizVeterinario;
source /caminho/para/database_completo.sql;
```

---

### **2️⃣ Configurar Credenciais do Banco**

Edite o arquivo `config.php` e atualize as credenciais:

```php
<?php
// CONFIGURAÇÕES DO SERVIDOR
define('DB_HOST', 'localhost');        // Host do banco (geralmente localhost)
define('DB_USER', 'seu_usuario');      // Usuário do MySQL
define('DB_PASS', 'sua_senha');        // Senha do MySQL
define('DB_NAME', 'QuizVeterinario');  // Nome do banco
```

**⚠️ IMPORTANTE:** 
- No servidor de produção, **NUNCA** use credenciais padrão
- Use senhas fortes e únicas
- Considere usar variáveis de ambiente para credenciais sensíveis

---

### **3️⃣ Fazer Upload dos Arquivos**

#### Estrutura de pastas no servidor:
```
/public_html/ (ou /www/ ou /htdocs/)
├── index.php
├── quiz.php
├── resultado.php
├── admin.php
├── config.php
└── style.css
```

#### Via FTP/SFTP:
1. Conecte-se ao servidor via FileZilla ou similar
2. Navegue até a pasta pública (public_html, www, htdocs)
3. Faça upload de todos os arquivos PHP e CSS

#### Via SSH/Terminal:
```bash
# Exemplo usando SCP
scp -r *.php *.css usuario@seu-servidor:/caminho/public_html/
```

---

### **4️⃣ Configurar Permissões**

```bash
# Definir permissões corretas (SSH)
chmod 644 *.php
chmod 644 *.css
chmod 755 /caminho/public_html/
```

---

### **5️⃣ Configurar Senha do Administrador**

Edite o arquivo `admin.php` na linha 7:

```php
// Altere a senha padrão
define('ADMIN_PASSWORD', 'SUA_SENHA_FORTE_AQUI');
```

**Recomendações:**
- Use uma senha forte (mínimo 12 caracteres)
- Combine letras, números e símbolos
- Não use senhas óbvias

---

### **6️⃣ Testar a Aplicação**

1. **Teste a página inicial:**
   - Acesse: `http://seu-dominio.com/index.php`
   - Verifique se o ranking aparece (vazio inicialmente)

2. **Teste o quiz:**
   - Digite um nome e inicie o quiz
   - Responda algumas questões
   - Verifique se o cronômetro funciona
   - Finalize e veja os resultados

3. **Teste o painel admin:**
   - Acesse: `http://seu-dominio.com/admin.php`
   - Faça login com a senha configurada
   - Teste a exportação CSV
   - Verifique as estatísticas

---

## 🔒 SEGURANÇA RECOMENDADA

### **1. Proteção do config.php**

Adicione ao `.htaccess`:
```apache
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

### **2. Usar HTTPS**

Configure SSL/TLS no servidor:
- Use Let's Encrypt (gratuito)
- Force redirecionamento HTTP → HTTPS

### **3. Backup Regular**

Configure backups automáticos:
- Banco de dados (diário)
- Arquivos (semanal)

### **4. Proteção contra SQL Injection**

✅ **JÁ IMPLEMENTADO:**
- Prepared Statements em todas as queries
- Validação de inputs
- Sanitização de dados

---

## 📊 VERIFICAÇÕES PÓS-DEPLOY

### ✅ Checklist de Testes:

- [ ] Banco de dados criado com sucesso
- [ ] 20 questões inseridas corretamente
- [ ] Página inicial carrega sem erros
- [ ] Formulário de nome funciona
- [ ] Quiz exibe todas as 20 questões
- [ ] Cronômetro funciona corretamente
- [ ] Respostas são salvas no banco
- [ ] Página de resultados mostra pontuação e tempo
- [ ] Ranking ordena por pontuação e tempo
- [ ] Painel admin acessível com senha
- [ ] Exportação CSV funciona
- [ ] Limpeza de ranking funciona
- [ ] Estatísticas são calculadas corretamente

---

## 🐛 RESOLUÇÃO DE PROBLEMAS

### **Erro: "Unknown column 'tempo_segundos'"**
**Solução:** Execute novamente o `database_completo.sql`

### **Erro: "mysqli extension not found"**
**Solução:** Ative a extensão mysqli no php.ini:
```ini
extension=mysqli
```

### **Erro 500 - Internal Server Error**
**Solução:** 
1. Verifique permissões dos arquivos
2. Confira credenciais do banco em config.php
3. Ative display_errors no php.ini temporariamente

### **Página em branco**
**Solução:**
1. Ative error reporting:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
2. Verifique logs do PHP e Apache

---

## 📝 CONFIGURAÇÕES OPCIONAIS

### **Alterar URL Amigável**

Crie um `.htaccess`:
```apache
RewriteEngine On
RewriteBase /

# Remover .php da URL
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^(.*)$ $1.php [L]

# Forçar HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### **Configurar Timezone**

No `config.php`, adicione:
```php
date_default_timezone_set('America/Sao_Paulo');
```

---

## 📞 SUPORTE

### **Logs Úteis:**
- Erros PHP: `/var/log/php_errors.log`
- Erros Apache: `/var/log/apache2/error.log`
- Erros MySQL: `/var/log/mysql/error.log`

### **Comandos Úteis:**

```bash
# Verificar status do MySQL
systemctl status mysql

# Verificar status do Apache/Nginx
systemctl status apache2
# ou
systemctl status nginx

# Ver logs em tempo real
tail -f /var/log/apache2/error.log
```

---

## 🎉 DEPLOY COMPLETO!

Após seguir todos os passos, seu Quiz Veterinário estará online e funcionando!

**Credenciais Padrão (MUDE IMEDIATAMENTE):**
- Senha Admin: `admin123`

**URLs Principais:**
- Quiz: `http://seu-dominio.com/`
- Admin: `http://seu-dominio.com/admin.php`

---

**Desenvolvido com ❤️ para Naara**
**Data:** Novembro 2025
