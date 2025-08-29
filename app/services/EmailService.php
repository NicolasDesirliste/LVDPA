<?php
// app/services/EmailService.php
namespace App\Services;

use App\Models\User;

/**
 * Classe EmailService
 * 
 * Responsabilité UNIQUE : Gérer l'envoi des emails
 * - Email de vérification
 * - Email de réinitialisation de mot de passe
 * - Notifications
 */
class EmailService {
    private string $fromEmail;
    private string $fromName;
    private string $siteUrl;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->fromEmail = 'noreply@lvdpa.fr';
        $this->fromName = 'LVDPA - La Voix Des Pères Abandonnés';
        $this->siteUrl = 'http://localhost/LVDPA'; // AFPT: Mettre dans config
    }
    
    /**
     * Envoie un email de vérification
     * 
     * @param User $user
     * @return bool
     */
    public function sendVerificationEmail(User $user): bool {
        $to = $user->get('email');
        $subject = "Vérifiez votre compte LVDPA";
        
        $data = [
            'pseudo' => $user->get('pseudo'),
            'url' => "{$this->siteUrl}/verify/{$user->get('token')}"
        ];
        
        $message = $this->loadTemplate('verification', $data);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Envoie un email de réinitialisation de mot de passe
     * 
     * @param User $user
     * @param string $resetToken
     * @return bool
     */
    public function sendPasswordResetEmail(User $user, string $resetToken): bool {
        $to = $user->get('email');
        $subject = "Réinitialisation de votre mot de passe LVDPA";
        
        $data = [
            'pseudo' => $user->get('pseudo'),
            'url' => "{$this->siteUrl}/reset-password/{$resetToken}"
        ];
        
        $message = $this->loadTemplate('password-reset', $data);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Envoie une notification de nouveau message
     * 
     * @param User $recipient
     * @param string $senderPseudo
     * @return bool
     */
    public function sendNewMessageNotification(User $recipient, string $senderPseudo): bool {
        $to = $recipient->get('email');
        $subject = "Nouveau message de {$senderPseudo} sur LVDPA";
        
        $data = [
            'pseudo' => $recipient->get('pseudo'),
            'senderPseudo' => $senderPseudo,
            'url' => "{$this->siteUrl}/messages"
        ];
        
        $message = $this->loadTemplate('new-message', $data);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Envoie un email de confirmation de validation professionnelle
     * 
     * @param User $professional
     * @return bool
     */
    public function sendProfessionalApprovalEmail(User $professional): bool {
        $to = $professional->get('email');
        $subject = "Votre compte professionnel LVDPA a été validé";
        
        $data = [
            'pseudo' => $professional->get('pseudo'),
            'url' => "{$this->siteUrl}/profile"
        ];
        
        $message = $this->loadTemplate('professional-approval', $data);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Méthode générique d'envoi d'email
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    private function send(string $to, string $subject, string $message): bool {
        $headers = [
            'From' => "{$this->fromName} <{$this->fromEmail}>",
            'Reply-To' => $this->fromEmail,
            'X-Mailer' => 'PHP/' . phpversion(),
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8'
        ];
        
        $headersString = '';
        foreach ($headers as $key => $value) {
            $headersString .= "{$key}: {$value}\r\n";
        }
        
        // En développement, logger au lieu d'envoyer
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            error_log("Email to: {$to}");
            error_log("Subject: {$subject}");
            error_log("Message: " . strip_tags($message));
            return true;
        }
        
        // En production, utiliser mail() ou un service SMTP
        return mail($to, $subject, $message, $headersString);
    }
    
    /**
     * Charge un template d'email
     * 
     * @param string $template Nom du template
     * @param array $data Données à passer au template
     * @return string
     */
    private function loadTemplate(string $template, array $data): string {
        $templatePath = dirname(__DIR__) . "/templates/emails/{$template}.php";
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template email '{$template}' introuvable");
        }
        
        // Extraire les variables pour le template
        extract($data);
        
        // Capturer le contenu du template
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}