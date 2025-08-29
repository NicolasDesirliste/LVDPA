<?php
// app/services/EmailService.php
namespace App\Services; // Namespace dans le dossier Services

use App\Models\User; // Import du modèle User

/**
 * Classe EmailService
 * 
 * Responsabilité UNIQUE : Gérer l'envoi des emails
 * - Email de vérification
 * - Email de réinitialisation de mot de passe
 * - Notifications
 */         /* Pense-bête: 
        L’en-tête MIME-Version: 1.0 dans un email indique que le message utilise la norme MIME (Multipurpose Internet Mail Extensions), 
        ce qui permet d’envoyer des contenus plus riches que du texte brut, comme des pièces jointes, du texte HTML ou des messages composés de plusieurs parties. 
        Cette indication est essentielle pour que les logiciels de messagerie sachent correctement interpréter et afficher les emails enrichis, 
        notamment le formatage HTML et les fichiers joints, ce qui ne serait pas possible avec le simple format texte.
        */



 // spolier alert: AFPT => 'A Faire Plus Tard' 
class EmailService { // Déclaration de la classe
    private string $fromEmail; // Propriété privée pour l'email expéditeur
    private string $fromName; // Propriété privée pour le nom expéditeur
    private string $siteUrl; // Propriété privée pour l'URL du site
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        $this->fromEmail = 'noreply@lvdpa.fr'; // Définit l'email expéditeur
        $this->fromName = 'LVDPA - La Voix Des Pères Abandonnés'; // Définit le nom expéditeur
        $this->siteUrl = 'http://localhost/LVDPA'; // Définit l'URL du site (AFPT: à mettre dans config)
    }
    
    /**
     * Envoie un email de vérification
     * 
     * @param User $user
     * @return bool
     */
    public function sendVerificationEmail(User $user): bool { // Prend un User et retourne un booléen
        $to = $user->get('email'); // Récupère l'email du destinataire
        $subject = "Vérifiez votre compte LVDPA"; // Définit le sujet
        
        $data = [ // Prépare les données pour le template
            'pseudo' => $user->get('pseudo'), // Pseudo de l'utilisateur
            'url' => "{$this->siteUrl}/verify/{$user->get('token')}" // URL de vérification avec token
        ];
        
        $message = $this->loadTemplate('verification', $data); // Charge le template avec les données
        
        return $this->send($to, $subject, $message); // Envoie l'email et retourne le résultat
    }
    
    /**
     * Envoie un email de réinitialisation de mot de passe
     * 
     * @param User $user
     * @param string $resetToken
     * @return bool
     */
    public function sendPasswordResetEmail(User $user, string $resetToken): bool { // Prend User et token
        $to = $user->get('email'); // Récupère l'email
        $subject = "Réinitialisation de votre mot de passe LVDPA"; // Définit le sujet
        
        $data = [ // Prépare les données
            'pseudo' => $user->get('pseudo'), // Pseudo
            'url' => "{$this->siteUrl}/reset-password/{$resetToken}" // URL de reset avec token
        ];
        
        $message = $this->loadTemplate('password-reset', $data); // Charge le template
        
        return $this->send($to, $subject, $message); // Envoie et retourne le résultat
    }
    
    /**
     * Envoie une notification de nouveau message
     * 
     * @param User $recipient
     * @param string $senderPseudo
     * @return bool
     */
    public function sendNewMessageNotification(User $recipient, string $senderPseudo): bool { // Prend destinataire et pseudo expéditeur
        $to = $recipient->get('email'); // Email du destinataire
        $subject = "Nouveau message de {$senderPseudo} sur LVDPA"; // Sujet avec le pseudo de l'expéditeur
        
        $data = [ // Prépare les données
            'pseudo' => $recipient->get('pseudo'), // Pseudo du destinataire
            'senderPseudo' => $senderPseudo, // Pseudo de l'expéditeur
            'url' => "{$this->siteUrl}/messages" // URL vers les messages
        ];
        
        $message = $this->loadTemplate('new-message', $data); // Charge le template
        
        return $this->send($to, $subject, $message); // Envoie et retourne le résultat
    }
    
    /**
     * Envoie un email de confirmation de validation professionnelle
     * 
     * @param User $professional
     * @return bool
     */
    public function sendProfessionalApprovalEmail(User $professional): bool { // Prend un professionnel
        $to = $professional->get('email'); // Email du professionnel
        $subject = "Votre compte professionnel LVDPA a été validé"; // Sujet
        
        $data = [ // Prépare les données
            'pseudo' => $professional->get('pseudo'), // Pseudo
            'url' => "{$this->siteUrl}/profile" // URL vers le profil
        ];
        
        $message = $this->loadTemplate('professional-approval', $data); // Charge le template
        
        return $this->send($to, $subject, $message); // Envoie et retourne le résultat
    }
    
    /**
     * Méthode générique d'envoi d'email
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    private function send(string $to, string $subject, string $message): bool { // Méthode privée d'envoi
        $headers = [ // Prépare les en-têtes
            'From' => "{$this->fromName} <{$this->fromEmail}>", // En-tête From avec nom et email
            'Reply-To' => $this->fromEmail, // Email de réponse
            'X-Mailer' => 'PHP/' . phpversion(), // Version PHP utilisée
            'MIME-Version' => '1.0', // Version MIME
            'Content-Type' => 'text/html; charset=UTF-8' // Type de contenu HTML UTF-8
        ];
        

        $headersString = ''; // Initialise la chaîne d'en-têtes
        foreach ($headers as $key => $value) { // Parcourt les en-têtes
            $headersString .= "{$key}: {$value}\r\n"; // Ajoute chaque en-tête avec retour à la ligne
        }
        
        // En développement, logger au lieu d'envoyer
        if ($_SERVER['SERVER_NAME'] === 'localhost') { // Si on est en local
            error_log("Email to: {$to}"); // Log le destinataire
            error_log("Subject: {$subject}"); // Log le sujet
            error_log("Message: " . strip_tags($message)); // Log le message sans HTML
            return true; // Retourne true (simulation d'envoi réussi)
        }
        
        // En production, utiliser mail() ou un service SMTP
        return mail($to, $subject, $message, $headersString); // Envoie l'email avec la fonction PHP mail()
    }
    
    /**
     * Charge un template d'email
     * 
     * @param string $template Nom du template
     * @param array $data Données à passer au template
     * @return string
     */
    private function loadTemplate(string $template, array $data): string { // Méthode privée pour charger un template
        $templatePath = dirname(__DIR__) . "/templates/emails/{$template}.php"; // Construit le chemin du template
        
        if (!file_exists($templatePath)) { // Si le fichier n'existe pas
            throw new \Exception("Template email '{$template}' introuvable"); // Lance une exception
        }
        
        // Extraire les variables pour le template
        extract($data); // Transforme les clés du tableau en variables
        
        // Capturer le contenu du template
        ob_start(); // Démarre la capture de sortie
        include $templatePath; // Inclut le template
        return ob_get_clean(); // Récupère le contenu capturé et nettoie le buffer
    }
}