<?php
/**
 * Centro Service - Contact Form Handler
 * Processa formulários de contato de forma segura
 */

// Headers para CORS e JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configurações
const ADMIN_EMAIL = 'contato@centroservice.com.br';
const SMTP_HOST = 'localhost'; // Ou seu servidor SMTP
const SMTP_PORT = 587;
const COMPANY_NAME = 'Centro Service';

// Função para sanitizar dados
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Função para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Função para validar telefone brasileiro
function isValidPhone($phone) {
    $pattern = '/^\(\d{2}\)\s\d{4,5}-\d{4}$/';
    return preg_match($pattern, $phone);
}

// Função para gerar token CSRF (opcional, para maior segurança)
function generateCSRFToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

// Função para verificar token CSRF
function verifyCSRFToken($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Função para enviar email
function sendEmail($to, $subject, $body, $fromName, $fromEmail) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $fromEmail,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $body, implode("\r\n", $headers));
}

// Função para salvar em arquivo de log/backup
function saveToFile($data) {
    $filename = __DIR__ . '/contacts_' . date('Y-m') . '.log';
    $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data) . PHP_EOL;
    
    // Criar diretório se não existir
    if (!file_exists(dirname($filename))) {
        mkdir(dirname($filename), 0755, true);
    }
    
    return file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
}

// Função para criar template de email
function createEmailTemplate($data) {
    $serviceNames = [
        'eletrica' => 'Serviços Elétricos',
        'hidraulica' => 'Serviços Hidráulicos',
        'pintura' => 'Pintura Profissional',
        'ar-condicionado' => 'Ar Condicionado',
        'manutencao' => 'Manutenção Geral',
        'emergencia' => 'Emergência 24h'
    ];
    
    $serviceName = $serviceNames[$data['service']] ?? $data['service'];
    
    $template = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Novo Contato - Centro Service</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background: #f8f9fa;
            }
            .email-container {
                background: white;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #1e40af;
            }
            .logo {
                font-size: 24px;
                font-weight: bold;
                color: #1e40af;
                margin-bottom: 10px;
            }
            .title {
                font-size: 20px;
                color: #374151;
                margin: 0;
            }
            .field {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #1e40af;
            }
            .field-label {
                font-weight: bold;
                color: #1e40af;
                margin-bottom: 5px;
                display: block;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .field-value {
                color: #374151;
                font-size: 16px;
                word-wrap: break-word;
            }
            .service-badge {
                display: inline-block;
                background: linear-gradient(135deg, #1e40af, #3b82f6);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 600;
            }
            .footer {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                text-align: center;
                color: #6b7280;
                font-size: 14px;
            }
            .priority {
                background: linear-gradient(135deg, #dc2626, #ef4444);
                color: white;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <div class="logo">🔧 Centro Service</div>
                <h1 class="title">Nova Solicitação de Orçamento</h1>
            </div>
            
            ' . ($data['service'] === 'emergencia' ? '<div class="priority">⚠️ ATENDIMENTO EMERGENCIAL - PRIORIDADE ALTA</div>' : '') . '
            
            <div class="field">
                <span class="field-label">Nome Completo</span>
                <div class="field-value">' . htmlspecialchars($data['name']) . '</div>
            </div>
            
            <div class="field">
                <span class="field-label">Telefone</span>
                <div class="field-value">
                    <a href="tel:' . preg_replace('/[^\d+]/', '', $data['phone']) . '" style="color: #1e40af; text-decoration: none;">
                        ' . htmlspecialchars($data['phone']) . '
                    </a>
                </div>
            </div>
            
            <div class="field">
                <span class="field-label">E-mail</span>
                <div class="field-value">
                    <a href="mailto:' . htmlspecialchars($data['email']) . '" style="color: #1e40af; text-decoration: none;">
                        ' . htmlspecialchars($data['email']) . '
                    </a>
                </div>
            </div>
            
            <div class="field">
                <span class="field-label">Serviço Solicitado</span>
                <div class="field-value">
                    <span class="service-badge">' . $serviceName . '</span>
                </div>
            </div>
            
            <div class="field">
                <span class="field-label">Mensagem</span>
                <div class="field-value">' . nl2br(htmlspecialchars($data['message'])) . '</div>
            </div>
            
            <div class="footer">
                <p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>
                <p><strong>IP:</strong> ' . $_SERVER['REMOTE_ADDR'] . '</p>
                <p>Este e-mail foi enviado automaticamente pelo sistema do site Centro Service.</p>
                
                <div style="margin-top: 20px;">
                    <a href="tel:' . preg_replace('/[^\d+]/', '', $data['phone']) . '" 
                       style="display: inline-block; background: #25D366; color: white; 
                              padding: 12px 24px; border-radius: 25px; text-decoration: none; 
                              margin-right: 10px; font-weight: bold;">
                        📞 Ligar para Cliente
                    </a>
                    
                    <a href="https://wa.me/' . preg_replace('/[^\d]/', '', $data['phone']) . '?text=Olá%20' . urlencode($data['name']) . '%21%20Aqui%20é%20da%20Centro%20Service.%20Recebemos%20sua%20solicitação%20de%20orçamento%20para%20' . urlencode($serviceName) . '.%20Vamos%20entrar%20em%20contato%20em%20breve%21" 
                       style="display: inline-block; background: #25D366; color: white; 
                              padding: 12px 24px; border-radius: 25px; text-decoration: none; 
                              font-weight: bold;">
                        💬 WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return $template;
}

// Função para criar template de confirmação para cliente
function createClientEmailTemplate($data) {
    $serviceNames = [
        'eletrica' => 'Serviços Elétricos',
        'hidraulica' => 'Serviços Hidráulicos',
        'pintura' => 'Pintura Profissional',
        'ar-condicionado' => 'Ar Condicionado',
        'manutencao' => 'Manutenção Geral',
        'emergencia' => 'Emergência 24h'
    ];
    
    $serviceName = $serviceNames[$data['service']] ?? $data['service'];
    
    $template = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmação de Solicitação - Centro Service</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background: #f8f9fa;
            }
            .email-container {
                background: white;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding: 30px;
                background: linear-gradient(135deg, #1e40af, #3b82f6);
                border-radius: 12px;
                color: white;
            }
            .logo {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .title {
                font-size: 20px;
                margin: 0;
            }
            .content {
                margin-bottom: 30px;
            }
            .service-info {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                border-left: 4px solid #1e40af;
                margin: 20px 0;
            }
            .service-badge {
                display: inline-block;
                background: linear-gradient(135deg, #1e40af, #3b82f6);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 10px;
            }
            .next-steps {
                background: #e0f2fe;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .contact-info {
                background: #f0f9ff;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: center;
            }
            .btn {
                display: inline-block;
                background: #25D366;
                color: white;
                padding: 12px 24px;
                border-radius: 25px;
                text-decoration: none;
                font-weight: bold;
                margin: 5px;
            }
            .footer {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                text-align: center;
                color: #6b7280;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <div class="logo">🔧 Centro Service</div>
                <h1 class="title">Obrigado pelo seu contato!</h1>
            </div>
            
            <div class="content">
                <p>Olá, <strong>' . htmlspecialchars($data['name']) . '</strong>!</p>
                
                <p>Recebemos sua solicitação de orçamento e agradecemos pela confiança em nossos serviços.</p>
                
                <div class="service-info">
                    <span class="service-badge">' . $serviceName . '</span>
                    <p><strong>Sua mensagem:</strong></p>
                    <p style="font-style: italic;">"' . htmlspecialchars($data['message']) . '"</p>
                </div>
                
                <div class="next-steps">
                    <h3 style="margin-top: 0; color: #0369a1;">📋 Próximos Passos</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li>Nossa equipe analisará sua solicitação</li>
                        <li>Entraremos em contato em <strong>até 2 horas</strong></li>
                        <li>Agendaremos uma visita técnica gratuita</li>
                        <li>Apresentaremos um orçamento detalhado</li>
                    </ul>
                </div>
                
                ' . ($data['service'] === 'emergencia' ? '
                <div style="background: #fecaca; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; color: #991b1b;">
                    <h3 style="margin-top: 0;">🚨 ATENDIMENTO EMERGENCIAL</h3>
                    <p>Sua solicitação foi marcada como <strong>URGENTE</strong>.</p>
                    <p>Nossa equipe entrará em contato em <strong>máximo 30 minutos</strong>.</p>
                    <p>Para contato imediato: <strong>(21) 96598-2113</strong></p>
                </div>
                ' : '') . '
                
                <div class="contact-info">
                    <h3 style="margin-top: 0; color: #1e40af;">📞 Precisa de Atendimento Imediato?</h3>
                    <p>Nossa equipe está disponível:</p>
                    <p><strong>Seg-Sex:</strong> 8h às 18h | <strong>Sáb:</strong> 8h às 16h</p>
                    <p><strong>Emergências:</strong> 24h</p>
                    
                    <a href="tel:+5521965982113" class="btn">📞 Ligar Agora</a>
                    <a href="https://wa.me/5521965982113?text=Olá%21%20Recebi%20o%20e-mail%20de%20confirmação%20da%20Centro%20Service%20sobre%20minha%20solicitação%20de%20orçamento." class="btn">💬 WhatsApp</a>
                </div>
                
                <p>Qualquer dúvida, não hesite em entrar em contato conosco!</p>
                
                <p>Atenciosamente,<br>
                <strong>Equipe Centro Service</strong></p>
            </div>
            
            <div class="footer">
                <p><strong>Centro Service - Sua casa em boas mãos</strong></p>
                <p>📍 Rio de Janeiro - RJ - Atendemos Barra da Tijuca, Zona sul e Centro | 📞 (21) 96598-2113 | ✉️ contato@centroservice.com.br</p>
                <p style="font-size: 12px; margin-top: 15px;">
                    Este é um e-mail automático de confirmação. Por favor, não responda diretamente a este e-mail.
                </p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return $template;
}

// Processar apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Validar dados obrigatórios
    $requiredFields = ['name', 'phone', 'email', 'service', 'message'];
    $errors = [];
    $data = [];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "O campo {$field} é obrigatório";
        } else {
            $data[$field] = sanitizeInput($_POST[$field]);
        }
    }
    
    // Validações específicas
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = "E-mail inválido";
    }
    
    if (!empty($data['phone']) && !isValidPhone($data['phone'])) {
        $errors[] = "Telefone deve estar no formato (XX) XXXXX-XXXX";
    }
    
    // Validar serviço
    $allowedServices = ['eletrica', 'hidraulica', 'pintura', 'ar-condicionado', 'manutencao', 'emergencia'];
    if (!empty($data['service']) && !in_array($data['service'], $allowedServices)) {
        $errors[] = "Serviço inválido";
    }
    
    // Se há erros, retornar
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Adicionar dados extras
    $data['timestamp'] = date('Y-m-d H:i:s');
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Criar templates de e-mail
    $adminEmailBody = createEmailTemplate($data);
    $clientEmailBody = createClientEmailTemplate($data);
    
    // Assunto dos e-mails
    $adminSubject = '[Centro Service] Nova Solicitação de Orçamento' . ($data['service'] === 'emergencia' ? ' - EMERGÊNCIA' : '');
    $clientSubject = 'Confirmação de Solicitação - Centro Service';
    
    // Enviar e-mail para admin
    $adminEmailSent = sendEmail(
        ADMIN_EMAIL,
        $adminSubject,
        $adminEmailBody,
        COMPANY_NAME,
        'noreply@centroservice.com.br'
    );
    
    // Enviar e-mail de confirmação para cliente
    $clientEmailSent = sendEmail(
        $data['email'],
        $clientSubject,
        $clientEmailBody,
        COMPANY_NAME,
        ADMIN_EMAIL
    );
    
    // Salvar em arquivo de backup
    $savedToFile = saveToFile($data);
    
    // Resposta de sucesso
    $response = [
        'success' => true,
        'message' => 'Solicitação enviada com sucesso! Entraremos em contato em breve.',
        'data' => [
            'admin_email_sent' => $adminEmailSent,
            'client_email_sent' => $clientEmailSent,
            'saved_to_file' => $savedToFile,
            'timestamp' => $data['timestamp']
        ]
    ];
    
    // Log de debug (remover em produção)
    if (defined('DEBUG') && DEBUG) {
        $response['debug'] = [
            'admin_email' => ADMIN_EMAIL,
            'data' => $data
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log do erro
    error_log('Contact form error: ' . $e->getMessage());
    
    // Resposta de erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor. Tente novamente ou entre em contato via WhatsApp.',
        'error_code' => 'INTERNAL_ERROR'
    ]);
}
?>