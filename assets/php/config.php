<?php
/**
 * Centro Service - Configurações do Sistema
 */

// Configurações de E-mail
define('ADMIN_EMAIL', 'contato@centroservice.com.br');
define('COMPANY_NAME', 'Centro Service');
define('COMPANY_PHONE', '(21) 96598-2113');
define('COMPANY_WHATSAPP', '5521965982113');
define('COMPANY_ADDRESS', 'Rio de Janeiro - RJ');

// Configurações de SMTP (se necessário)
define('SMTP_HOST', 'smtp.gmail.com'); // ou seu provedor SMTP
define('SMTP_PORT', 587);
define('SMTP_USERNAME', ''); // seu email SMTP
define('SMTP_PASSWORD', ''); // sua senha SMTP
define('SMTP_SECURE', 'tls'); // tls ou ssl

// Configurações de Segurança
define('ENABLE_CSRF', true); // Habilitar proteção CSRF
define('RATE_LIMIT_ENABLED', true); // Limitar tentativas por IP
define('RATE_LIMIT_ATTEMPTS', 5); // Máx tentativas por hora
define('HONEYPOT_ENABLED', true); // Campo honeypot anti-spam

// Configurações de Arquivo
define('LOG_CONTACTS', true); // Salvar contatos em arquivo
define('LOG_DIRECTORY', __DIR__ . '/logs/');

// Configurações de Debug
define('DEBUG_MODE', false); // Apenas em desenvolvimento
define('DISPLAY_ERRORS', false);

// Configurações de Horário
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Serviços
$servicesList = [
    'eletrica' => 'Serviços Elétricos',
    'hidraulica' => 'Serviços Hidráulicos', 
    'pintura' => 'Pintura Profissional',
    'ar-condicionado' => 'Ar Condicionado',
    'manutencao' => 'Manutenção Geral',
    'emergencia' => 'Emergência 24h'
];

// Configurações de E-mail Templates
$emailSettings = [
    'admin_template_color' => '#1e40af',
    'client_template_color' => '#1e40af',
    'emergency_color' => '#DC2626',
    'success_color' => '#1e40af'
];

// Função para verificar se é ambiente de desenvolvimento
function isDevelopment() {
    return in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:3000']);
}

// Configurações condicionais baseadas no ambiente
if (isDevelopment()) {
    define('ENVIRONMENT', 'development');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    define('ENVIRONMENT', 'production');
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>