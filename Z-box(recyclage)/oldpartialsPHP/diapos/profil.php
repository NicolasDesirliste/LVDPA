<div class="profile-container">
    <?php
    // Utiliser directement les données de session pour déterminer le type d'utilisateur
    $userType = isset($_SESSION['user_type']) ? strtolower($_SESSION['user_type']) : 'inconnu';
    ?>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash-message">
            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
            <?php unset($_SESSION['flash_message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-header">
        <h2>Mon Profil</h2>
        <p>Bienvenue sur votre espace personnel. Ici, vous pouvez gérer vos informations et vos préférences.</p>
    </div>
    
    <div class="profile-content">
        <?php if (isset($this->data['profile']) && $this->data['profile']): ?>
            <div class="profile-card">
                <div class="profile-photo">
                    <?php if (isset($this->data['profile']['photo']) && !empty($this->data['profile']['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($this->data['profile']['photo']); ?>" alt="Photo de profil">
                    <?php else: ?>
                        <img src="/LVDPA/public/assets/img/no-profile-pic.png" alt="Photo de profil par défaut">
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h3><?php echo isset($this->data['profile']['prenom'], $this->data['profile']['nom']) ? htmlspecialchars($this->data['profile']['prenom'] . ' ' . $this->data['profile']['nom']) : 'Profil incomplet'; ?></h3>
                    <p><strong>Type d'utilisateur:</strong> <?php echo htmlspecialchars(ucfirst($userType)); ?></p>
                    <p><strong>Email:</strong> <?php echo isset($this->data['profile']['email']) ? htmlspecialchars($this->data['profile']['email']) : htmlspecialchars($_SESSION['user_email']); ?></p>
                    
                    <?php if (isset($this->data['profile']['telephone']) && !empty($this->data['profile']['telephone'])): ?>
                        <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($this->data['profile']['telephone']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (in_array($userType, ['avocat', 'psychologue', 'mediateur'])): ?>
                        <?php if (isset($this->data['profile']['adresse_cabinet']) && !empty($this->data['profile']['adresse_cabinet'])): ?>
                            <p><strong>Adresse du cabinet:</strong> <?php echo htmlspecialchars($this->data['profile']['adresse_cabinet']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (isset($this->data['profile']['heures_debut']) && isset($this->data['profile']['heures_fin'])): ?>
                            <p><strong>Heures de travail:</strong> <?php echo htmlspecialchars($this->data['profile']['heures_debut']); ?> - <?php echo htmlspecialchars($this->data['profile']['heures_fin']); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($this->data['profile']['preference_contact_debut']) && isset($this->data['profile']['preference_contact_fin'])): ?>
                            <p><strong>Préférence de contact:</strong> <?php echo htmlspecialchars($this->data['profile']['preference_contact_debut']); ?> - <?php echo htmlspecialchars($this->data['profile']['preference_contact_fin']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isset($this->data['profile']['biographie']) && !empty($this->data['profile']['biographie'])): ?>
                <div class="profile-section">
                    <h3>Biographie</h3>
                    <p><?php echo nl2br(htmlspecialchars($this->data['profile']['biographie'])); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (in_array($userType, ['avocat', 'psychologue', 'mediateur'])): ?>
                <?php if (isset($this->data['profile']['specialites']) && !empty($this->data['profile']['specialites'])): ?>
                    <div class="profile-section">
                        <h3>Spécialités</h3>
                        <ul>
                            <?php 
                            $specialites = explode(',', $this->data['profile']['specialites']);
                            foreach ($specialites as $specialite): 
                            ?>
                                <li><?php echo htmlspecialchars(trim($specialite)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($this->data['profile']['site_web']) && !empty($this->data['profile']['site_web'])): ?>
                    <div class="profile-section">
                        <h3>Site Web</h3>
                        <p><a href="<?php echo htmlspecialchars($this->data['profile']['site_web']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($this->data['profile']['site_web']); ?></a></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="profile-incomplete">
                <p>Votre profil n'est pas encore complet. Veuillez le mettre à jour en cliquant sur le bouton ci-dessous.</p>
            </div>
        <?php endif; ?>
        
        <div class="profile-actions">
            <?php if ($userType === 'particulier'): ?>
                <a href="/LVDPA/index.php?page=edit_profile_particulier" class="btn-primary">Modifier mon profil particulier</a>
            <?php elseif (in_array($userType, ['avocat', 'psychologue', 'mediateur'])): ?>
                <a href="/LVDPA/index.php?page=edit_profile_professionnel" class="btn-primary">Modifier mon profil professionnel</a>
            <?php else: ?>
                <p>Type d'utilisateur non reconnu: <?php echo htmlspecialchars($userType); ?></p>
                <a href="/LVDPA/index.php?page=edit_profile_professionnel" class="btn-primary">Modifier mon profil (option par défaut)</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/LVDPA/public/assets/css/profile.css">