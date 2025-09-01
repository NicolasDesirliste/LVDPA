<!-- Début de la vue du menu de recherche des professionnels -->
<div class="character-grid">
    <?php 
    // Ajouter un log pour voir la structure exacte des données
    error_log("Structure des données dans search_results.php: " . print_r($data ?? [], true));
    error_log("Structure des données professionals: " . print_r($professionals ?? 'Non défini', true));
    
    // Utiliser les variables qui sont passées à la vue
    $professionalsList = $professionals ?? ($data['professionals'] ?? []);
    ?>
    
    <?php if (!empty($professionalsList)): ?>
        <?php foreach ($professionalsList as $professional): ?>
            <div class="character-card">
                <div class="character-image-container">
                    <?php if (isset($professional['photo']) && !empty($professional['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($professional['photo']); ?>" alt="Photo de <?php echo htmlspecialchars($professional['prenom'] ?? 'Professionnel'); ?>" class="character-image" />
                    <?php else: ?>
                        <img src="/LVDPA/public/assets/img/superpapa.png" alt="Photo par défaut" class="character-image" />
                    <?php endif; ?>
                    <div
                        class="character-label <?php echo strtolower(htmlspecialchars($professional['protype'])) === 'avocat' ? 'com' : (strtolower(htmlspecialchars($professional['protype'])) === 'psychologue' ? 'syn' : 'med'); ?>">
                        <?php echo ucfirst(htmlspecialchars($professional['protype'])); ?>
                    </div>
                </div>
                <div class="character-stats">
                    <div class="stat">
                        <span class="stat-value"><i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($professional['prenom'] ?? 'Prénom'); ?>
                            <?php echo htmlspecialchars($professional['nom'] ?? 'Nom'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($professional['telephone'] ?? 'Non renseigné'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($professional['email']); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($professional['adresse_cabinet'] ?? $professional['city'] ?? 'Non renseigné'); ?>
                            (<?php echo htmlspecialchars($professional['departement']); ?>)</span>
                    </div>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/index.php?page=professional_profile&id=<?php echo htmlspecialchars($professional['user_id']); ?>" class="ff-button">Visiter mon profil</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-results">
            <p>Aucun professionnel trouvé correspondant à vos critères.</p>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="/LVDPA/public/assets/css/search_results.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">