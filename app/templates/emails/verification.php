<?php
// app/templates/emails/verification.php
?>
<html>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #2c3e50;'>Bienvenue sur LVDPA, <?= htmlspecialchars($pseudo) ?> !</h2>
        
        <p>Merci de vous être inscrit sur La Voix Des Pères Abandonnés.</p>
        
        <p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p>
        
        <p style='text-align: center; margin: 30px 0;'>
            <a href='<?= htmlspecialchars($url) ?>' 
               style='background-color: #3498db; color: white; padding: 12px 30px; 
                      text-decoration: none; border-radius: 5px; display: inline-block;'>
                Vérifier mon compte
            </a>
        </p>
        
        <p>Ou copiez ce lien dans votre navigateur :<br>
        <small><?= htmlspecialchars($url) ?></small></p>
        
        <p><strong>Ce lien expire dans 24 heures.</strong></p>
        
        <hr style='margin: 30px 0; border: none; border-top: 1px solid #ddd;'>
        
        <p style='font-size: 12px; color: #777;'>
            Si vous n'avez pas créé de compte sur LVDPA, ignorez cet email.
        </p>
    </div>
</body>
</html>