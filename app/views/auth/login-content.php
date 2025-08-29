<!-- app/views/auth/login-content.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/LVDPA/public/CSS/authcss/login.css">

<div data-page-title="Connexion à votre compte">
    <div class="login-container">
        <div class="form-header">
            <h2>Connexion à votre compte</h2>
        </div>
        
        <div class="info-text">
            <p>Connectez-vous pour profiter de toutes les fonctionnalités du site</p>
        </div>
        
        <div class="form-content">
            <form id="loginForm" action="/LVDPA/login" method="POST">
                <div class="input-container">
                    <i class="fa fa-user icon"></i>
                    <input class="input-field" 
                           type="text" 
                           placeholder="Entrez votre pseudonyme" 
                           name="pseudo"
                           id="pseudo"
                           required
                           value="">
                </div>
                
                <div class="input-container">
                    <i class="fa fa-envelope icon"></i>
                    <input class="input-field" 
                           type="email" 
                           placeholder="Entrez votre adresse email" 
                           name="email"
                           id="email" 
                           required
                           value="">
                </div>
                
                <div class="input-container">
                    <i class="fa fa-key icon"></i>
                    <input class="input-field" 
                           type="password" 
                           placeholder="Entrez votre mot de passe" 
                           name="mot_de_passe"
                           id="mot_de_passe" 
                           required>
                </div>

                <div class="login-options">
                    <label>
                        <input type="checkbox" name="remember" id="remember">
                        Se souvenir de moi
                    </label>
                    <a href="/LVDPA/forgot-password" class="forgot-password" data-spa-link>Mot de passe oublié?</a>
                </div>

                <!-- Zone pour afficher les messages d'erreur -->
                <?php if (isset($errors) && !empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Zone pour afficher les messages de succès -->
                <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn">Connexion</button>
                
                <div class="register-link">
                    Pas encore inscrit? <a href="/LVDPA/register" data-spa-link>S'inscrire</a>
                </div>
            </form>
        </div>
        
        <footer class="form-footer">
            <h3>Heureux de vous revoir parmi nous!</h3>
        </footer>
    </div>
    <script src="/LVDPA/public/js/authjs/login-form.js"></script>
</div>