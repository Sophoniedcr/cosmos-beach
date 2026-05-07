<?php
// ============================================================
// COSMOS BEACH — backend/config/EmailService.php — v3.1 CORRIGÉ
//
// CORRECTION PRINCIPALE :
//   dirname(__DIR__) remontait à backend/ et cherchait backend/.env
//   → Corrigé : dirname(__DIR__, 2) remonte à la RACINE du projet
//     backend/config/ → dirname = backend/ → dirname(x,2) = racine/
// ============================================================

require_once __DIR__ . '/../../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';

class EmailService
{
    private string $gmail_email    = '';
    private string $gmail_password = '';
    private string $from_name      = 'Cosmos Beach';

    private string $smtp_host     = 'smtp.gmail.com';
    private int    $smtp_port     = 587;
    private string $smtp_secure   = 'tls'; // 'tls' ou 'ssl'

    public function __construct()
    {
        $this->loadEmailConfig();
    }

    // ──────────────────────────────────────────────────────────
    // Chargement config — chemin .env CORRIGÉ
    // ──────────────────────────────────────────────────────────
    private function loadEmailConfig(): void
    {
        // __DIR__ = Application-Defense/backend/config
        // dirname(__DIR__, 2) = Application-Defense/  ← CORRECT
        $env_file = dirname(__DIR__, 2) . '/.env';

        if (file_exists($env_file)) {
            $env = parse_ini_file($env_file);
            $this->gmail_email    = trim($env['GMAIL_EMAIL']    ?? '');
            $this->gmail_password = trim($env['GMAIL_PASSWORD'] ?? '');
            $this->from_name      = trim($env['EMAIL_FROM_NAME'] ?? 'Cosmos Beach');
        } else {
            // Fallback sur les variables d'environnement système
            $this->gmail_email    = getenv('GMAIL_EMAIL')    ?: '';
            $this->gmail_password = getenv('GMAIL_PASSWORD') ?: '';
            error_log("[COSMOS][Email] .env introuvable à : $env_file");
        }

        // Détection automatique : Hostinger vs Gmail
        if (!empty($this->gmail_email) && !str_contains($this->gmail_email, 'gmail.com')) {
            // Email Hostinger ou autre fournisseur
            $this->smtp_host   = 'smtp.hostinger.com';
            $this->smtp_port   = 587;
            $this->smtp_secure = 'tls';
            error_log("[COSMOS][Email] Mode SMTP Hostinger activé pour : {$this->gmail_email}");
        } else {
            // Gmail
            $this->smtp_host   = 'smtp.gmail.com';
            $this->smtp_port   = 587;
            $this->smtp_secure = 'tls';
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->gmail_email) && !empty($this->gmail_password);
    }

    // ──────────────────────────────────────────────────────────
    // Envoi email OTP
    // ──────────────────────────────────────────────────────────
    public function sendOTPEmail(string $recipient_email, string $recipient_name, string $otp_code): bool
    {
        if (!$this->isConfigured()) {
            error_log("[COSMOS][Email] Service non configuré. Email: '{$this->gmail_email}', Password: " . (empty($this->gmail_password) ? 'VIDE' : 'OK'));
            return false;
        }

        $subject   = 'Code de vérification — Cosmos Beach';
        $html_body = $this->generateOTPEmailHTML($recipient_name, $otp_code);

        return $this->sendWithPhpMailer($recipient_email, $recipient_name, $subject, $html_body);
    }

    // ──────────────────────────────────────────────────────────
    // Envoi confirmation réinitialisation
    // ──────────────────────────────────────────────────────────
    public function sendPasswordResetConfirmation(string $recipient_email, string $recipient_name): bool
    {
        if (!$this->isConfigured()) return false;

        $subject   = 'Mot de passe réinitialisé — Cosmos Beach';
        $html_body = $this->generateConfirmationEmailHTML($recipient_name);

        return $this->sendWithPhpMailer($recipient_email, $recipient_name, $subject, $html_body);
    }

    // ──────────────────────────────────────────────────────────
    // Envoi email générique (HTML libre — tickets, notifications…)
    // ──────────────────────────────────────────────────────────
    public function sendRawEmail(string $to_email, string $to_name, string $subject, string $html_body): bool
    {
        if (!$this->isConfigured()) {
            error_log("[COSMOS][Email] Service non configuré — email non envoyé à $to_email");
            return false;
        }
        return $this->sendWithPhpMailer($to_email, $to_name, $subject, $html_body);
    }

    // ──────────────────────────────────────────────────────────
    // Envoi via PHPMailer (SMTP Gmail)
    // ──────────────────────────────────────────────────────────
    private function sendWithPhpMailer(string $to_email, string $to_name, string $subject, string $html): bool
    {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            // Debug SMTP — mettre à 0 en production, 2 pour voir les logs
            $mail->SMTPDebug  = 0;
            $mail->Debugoutput = function($msg, $level) {
                error_log("[COSMOS][SMTP] $msg");
            };

            $mail->isSMTP();
            $mail->Host       = $this->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->gmail_email;
            $mail->Password   = $this->gmail_password;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->smtp_port;
            $mail->CharSet    = 'UTF-8';

            // Timeout augmenté pour les connexions lentes
            $mail->Timeout    = 30;

            $mail->setFrom($this->gmail_email, $this->from_name);
            $mail->addAddress($to_email, $to_name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));

            $result = $mail->send();
            if ($result) {
                error_log("[COSMOS][Email] ✓ Email envoyé à $to_email");
            }
            return $result;

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("[COSMOS][Email] PHPMailer ERREUR: " . $e->getMessage());
            return false;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Templates HTML emails
    // ──────────────────────────────────────────────────────────
    private function generateOTPEmailHTML(string $name, string $otp): string
    {
        $otp_display  = implode(' ', str_split($otp, 3));
        $current_year = date('Y');
        $expiration   = $GLOBALS['otp_expiration_minutes'] ?? 15;

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Code OTP — Cosmos Beach</title>
  <style>
    body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
    .wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .header{background:linear-gradient(135deg,#0ea5e9,#4f46e5);padding:40px 30px;text-align:center;color:#fff}
    .header h1{margin:0;font-size:26px;font-weight:700}
    .header p{margin:6px 0 0;opacity:.85;font-size:14px}
    .body{padding:40px 30px}
    .greeting{font-size:16px;color:#1e293b;margin-bottom:20px}
    .otp-box{background:#f8fafc;border:2px solid #0ea5e9;border-radius:10px;padding:24px;text-align:center;margin:28px 0}
    .otp-label{font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#64748b;margin-bottom:10px}
    .otp-code{font-size:38px;font-weight:800;letter-spacing:8px;color:#0284c7;font-family:'Courier New',monospace}
    .otp-exp{font-size:12px;color:#94a3b8;margin-top:10px}
    .warning{background:#fefce8;border-left:4px solid #eab308;border-radius:6px;padding:14px 16px;font-size:13px;color:#854d0e;margin:20px 0}
    .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 30px;text-align:center;font-size:12px;color:#94a3b8}
    .footer a{color:#0ea5e9;text-decoration:none}
  </style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🏖️ Cosmos Beach</h1>
    <p>Réinitialisation de mot de passe</p>
  </div>
  <div class="body">
    <p class="greeting">Bonjour <strong>{$name}</strong>,</p>
    <p style="color:#475569">Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code à usage unique :</p>
    <div class="otp-box">
      <div class="otp-label">Votre code de vérification</div>
      <div class="otp-code">{$otp_display}</div>
      <div class="otp-exp">⏱ Expire dans {$expiration} minutes</div>
    </div>
    <div class="warning">
      ⚠️ <strong>Ne partagez jamais ce code.</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
    </div>
  </div>
  <div class="footer">
    <p>© {$current_year} Cosmos Beach — Email automatique, ne pas répondre.</p>
  </div>
</div>
</body>
</html>
HTML;
    }

    private function generateConfirmationEmailHTML(string $name): string
    {
        $current_year = date('Y');
        $base_url     = defined('BASE_URL') ? 'http://localhost' . BASE_URL : '#';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mot de passe réinitialisé — Cosmos Beach</title>
  <style>
    body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
    .wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .header{background:linear-gradient(135deg,#10b981,#059669);padding:40px 30px;text-align:center;color:#fff}
    .header h1{margin:0;font-size:26px}
    .body{padding:40px 30px;text-align:center}
    .icon{font-size:60px;margin-bottom:20px}
    .btn{display:inline-block;background:#10b981;color:#fff;padding:14px 36px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;margin-top:24px}
    .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px;text-align:center;font-size:12px;color:#94a3b8}
  </style>
</head>
<body>
<div class="wrap">
  <div class="header"><h1>✓ Mot de passe réinitialisé</h1></div>
  <div class="body">
    <div class="icon">✅</div>
    <p style="font-size:16px;color:#1e293b">Bonjour <strong>{$name}</strong>, votre mot de passe a été modifié avec succès.</p>
    <p style="color:#64748b">Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.</p>
    <a href="{$base_url}/?action=login" class="btn">Se connecter</a>
  </div>
  <div class="footer">© {$current_year} Cosmos Beach</div>
</div>
</body>
</html>
HTML;
    }
}
