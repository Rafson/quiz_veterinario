@echo off
echo ====================================
echo Habilitando extensao mysqli no PHP
echo ====================================
echo.

set PHP_INI=C:\php8.4\php.ini

if not exist "%PHP_INI%" (
    echo ERRO: Arquivo php.ini nao encontrado em %PHP_INI%
    pause
    exit /b 1
)

echo Fazendo backup do php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup" >nul

echo Habilitando mysqli...
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=mysqli', 'extension=mysqli' | Set-Content '%PHP_INI%'"

echo.
echo ====================================
echo Configuracao concluida!
echo ====================================
echo.
echo O que fazer agora:
echo 1. Feche o servidor PHP (Ctrl+C)
echo 2. Execute novamente: php -S localhost:8000
echo 3. Acesse: http://localhost:8000/testar_conexao.php
echo.
pause
