<?php
// app/templates/emails/resetpw.php
?>
<html>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #2c3e50;'>Réinitialisation de mot de passe</h2>
        
        <p>Bonjour <?= htmlspecialchars($pseudo) ?>,</p>
        
        <p>Vous avez demandé à réinitialiser votre mot de passe sur LVDPA.</p>
        
        <p style='text-align: center; margin: 30px 0;'>
            <a href='<?= htmlspecialchars($url) ?>' 
               style='background-color: #e74c3c; color: white; padding: 12px 30px; 
                      text-decoration: none; border-radius: 5px; display: inline-block;'>
                Réinitialiser mon mot de passe
            </a>
        </p>
        
        <p><strong>Ce lien expire dans 1 heure.</strong></p>
        
        <p style='font-size: 12px; color: #777;'>
            Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </p>
    </div>
</body>
</html>