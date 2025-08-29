<div class="professional-profile-container">
    <div class="profile-layout">
        <!-- Colonne gauche -->
        <div class="left-column">
            <!-- Carte principale du profil -->
            <div class="profile-card">
                <!-- Photo du profil au centre -->
                <div class="profile-header-with-image">
                    <div class="profile-image">
                        <?php if (isset($professional['photo']) && !empty($professional['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($professional['photo']); ?>" alt="Photo de <?php echo htmlspecialchars($professional['prenom'] . ' ' . $professional['nom']); ?>">
                        <?php else: ?>
                            <img src="/LVDPA/public/assets/img/no-profile-pic.png" alt="Photo de profil par défaut">
                        <?php endif; ?>
                    </div>
                    <div class="profile-header">
                        <h1><?php echo htmlspecialchars($professional['prenom'] . ' ' . $professional['nom']); ?></h1>
                        <?php 
                        // Utiliser le type_professionnel de la table profils par priorité
                        $professionalType = isset($professional['type_professionnel']) && !empty($professional['type_professionnel']) 
                            ? $professional['type_professionnel'] 
                            : ($professional['protype'] ?? '');
                        
                        // Formater pour l'affichage
                        switch($professionalType) {
                            case 'avocat':
                                $displayType = 'Avocat';
                                $typeClass = 'com';
                                $displayVerified = 'Avocat vérifié';
                                break;
                            case 'psychologue':
                                $displayType = 'Psychologue';
                                $typeClass = 'syn';
                                $displayVerified = 'Psychologue vérifié';
                                break;
                            case 'mediateur':
                                $displayType = 'Médiateur';
                                $typeClass = 'med';
                                $displayVerified = 'Médiateur vérifié';
                                break;
                            default:
                                $displayType = strtoupper($professionalType);
                                $typeClass = 'sab';
                                $displayVerified = ucfirst($professionalType) . ' vérifié';
                        }
                        ?>
                        <div class="professional-type <?php echo $typeClass; ?>">
                            <?php echo $displayType; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Informations de contact disposées verticalement -->
                <div class="profile-info">
                    <!-- Information rapide sur la réactivité -->
                    <?php if (isset($professional['repond_rapidement']) && $professional['repond_rapidement']): ?>
                    <div class="quick-reply">
                        <i class="fas fa-bolt-lightning"></i> Répond rapidement
                    </div>
                    <?php endif; ?>
                    
                    <!-- Informations de contact -->
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($professional['telephone']); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($professional['email']); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($professional['adresse_cabinet'] ?? 'Adresse non renseignée'); ?></p>
                    <p><i class="fas fa-clock"></i> Disponible de <?php echo htmlspecialchars($professional['heures_debut']); ?> à <?php echo htmlspecialchars($professional['heures_fin']); ?></p>
                    
                    <!-- Site web si renseigné -->
                    <?php if (isset($professional['site_web']) && !empty($professional['site_web'])): ?>
                    <p><i class="fas fa-globe"></i> <a href="<?php echo htmlspecialchars($professional['site_web']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($professional['site_web']); ?></a></p>
                    <?php endif; ?>
                </div>
                
                <!-- Boutons d'action -->
                <div class="profile-actions">
                    <a href="/LVDPA/index.php?page=contact&pro_id=<?php echo htmlspecialchars($professional['user_id']); ?>" class="btn-action">Contacter ce professionnel</a>
                    <a href="/LVDPA/index.php?page=search" class="btn-action">Retour aux résultats de recherche</a>
                    <a href="/LVDPA/index.php?page=report&id=<?php echo htmlspecialchars($professional['user_id']); ?>" class="btn-action btn-report">Signaler cet utilisateur</a>
                </div>
            </div>
            
            <!-- Horaires -->
            <?php if (isset($professional_hours) && !empty($professional_hours)): ?>
            <div class="profile-section hours-section">
                <h2>Horaires</h2>
                <div class="section-content">
                    <table class="hours-table">
                        <?php foreach ($professional_hours as $hour): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hour['jour_semaine']); ?></td>
                            <td><?php echo htmlspecialchars($hour['heure_debut']); ?> - <?php echo htmlspecialchars($hour['heure_fin']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- La section Derniers posts a été déplacée vers la colonne de droite -->
            
        </div>
        
        <!-- Colonne droite -->
        <div class="right-column">
            <!-- Navigation par onglets -->
            <div class="nav-tabs">
                <a href="#a-propos" class="nav-tab active" data-target="a-propos">A PROPOS</a>
                <a href="#presentation" class="nav-tab" data-target="presentation">PRÉSENTATION</a>
                <a href="#honoraires" class="nav-tab" data-target="honoraires">HONORAIRES</a>
                <a href="#adresse" class="nav-tab" data-target="adresse">ADRESSE</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                 <a href="#administration" class="nav-tab" data-target="administration">ADMINISTRATION</a>
                <?php endif; ?>
            </div>

            
            
            <!-- Section À propos -->
            <div id="a-propos" class="tab-content active">
                <!-- Informations principales -->
                <div class="profile-section about-section">
                    <h2>À propos</h2>
                    <div class="section-content">
                        <div class="about-content">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt info-icon"></i>
                                <span class="info-text"><?php echo htmlspecialchars($professional['adresse_cabinet'] . ' ' . $professional['departement']); ?></span>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-user-check info-icon"></i>
                                <span class="info-text <?php echo $typeClass; ?>">
                                    <?php echo $displayVerified; ?>
                                </span>
                            </div>
                            
                            <?php if (isset($professional['annee_debut_pratique']) && !empty($professional['annee_debut_pratique'])): ?>
                            <div class="info-item">
                                <i class="fas fa-history info-icon"></i>
                                <span class="info-text"><?php echo (date('Y') - $professional['annee_debut_pratique']); ?> ans d'expérience</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <i class="fas fa-gavel info-icon"></i>
                                <span class="info-text">
                                <?php if (isset($professional['accepte_aide_pro_deo']) && $professional['accepte_aide_pro_deo']): ?>
                                    Accepte aide pro deo
                                <?php else: ?>
                                    N'accepte pas aide pro deo
                                <?php endif; ?>
                                </span>
                            </div>
                            
                            <?php if (isset($professional['tarif_min']) && isset($professional['tarif_max'])): ?>
                            <div class="info-item">
                                <i class="fas fa-euro-sign info-icon"></i>
                                <span class="info-text">Entre <?php echo htmlspecialchars($professional['tarif_min']); ?> € et <?php echo htmlspecialchars($professional['tarif_max']); ?> €</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Domaines de droit/spécialités -->
                <?php if (isset($professional_domains) && !empty($professional_domains)): ?>
                <div class="profile-section domains-section">
                    <h2>Domaines</h2>
                    <div class="section-content">
                        <div class="tags-container">
                            <?php foreach ($professional_domains as $domain): ?>
                            <div class="tag"><?php echo htmlspecialchars($domain['domaine']); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Compétences -->
                <?php if (isset($professional_skills) && !empty($professional_skills)): ?>
                <div class="profile-section skills-section">
                    <h2>Compétences</h2>
                    <div class="section-content">
                        <div class="tags-container">
                            <?php foreach ($professional_skills as $skill): ?>
                            <div class="tag"><?php echo htmlspecialchars($skill['competence']); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Derniers posts - Déplacé ici depuis la colonne de gauche -->
                <div class="profile-section posts-section">
                    <h2>Derniers posts de <?php echo htmlspecialchars($professional['prenom']); ?></h2>
                    <div class="posts-container">
                        <?php 
                        // Si nous avons des posts à afficher
                        if (isset($professional_posts) && !empty($professional_posts)): 
                            foreach ($professional_posts as $post):
                        ?>
                            <div class="post-item-container">
                                <div class="post-item" data-post-id="<?php echo $post['id']; ?>">
                                    <div class="post-info">
                                        <span class="post-context">
                                        <?php if (isset($post['type_action']) && $post['type_action'] == 'post'): ?>
                                            A posté dans: <?php echo htmlspecialchars($post['categorie'] ?? 'Forum'); ?>
                                        <?php else: ?>
                                            A répondu à: <?php echo htmlspecialchars($post['pseudonyme_reference'] ?? 'un utilisateur'); ?>
                                        <?php endif; ?>
                                        </span>
                                        <span class="post-date">Le: <span class="date-value"><?php echo date('d/m/Y', strtotime($post['date_creation'])); ?></span></span>
                                    </div>
                                    <div class="post-content">
                                        <span class="post-title">
                                        <?php if (isset($post['type_action']) && $post['type_action'] == 'post'): ?>
                                            Titre: <?php echo htmlspecialchars($post['titre'] ?? ''); ?>
                                        <?php else: ?>
                                            dans: <?php echo htmlspecialchars($post['post_reference_titre'] ?? 'une discussion'); ?>
                                        <?php endif; ?>
                                        </span>
                                        <?php if (isset($post['marqueur']) && !empty($post['marqueur'])): ?>
                                        <span class="post-marker">Marqueur: <?php echo htmlspecialchars($post['marqueur']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="post-footer">
                                        <span class="post-arrow">→</span>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <div class="post-item-container">
                                <div class="post-item">
                                    <div class="post-content">
                                        Aucun post récent à afficher.
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Section Présentation/Bio -->
            <div id="presentation" class="tab-content">
                <div class="profile-section presentation-section">
                    <h2>Présentation</h2>
                    <div class="section-content">
                        <div class="presentation-text">
                            <?php echo nl2br(htmlspecialchars($professional['biographie'] ?? 'Aucune présentation disponible.')); ?>
                        </div>
                    </div>
                </div>
                
                <div class="profile-section verification-section">
                    <h2 class="<?php echo $typeClass; ?>">
                        <?php echo $displayVerified; ?>
                    </h2>
                    <div class="section-content">
                        <div class="presentation-text">
                            <?php if (isset($professional['annee_debut_pratique']) && !empty($professional['annee_debut_pratique'])): ?>
                            <p>Pratique depuis : <?php echo htmlspecialchars($professional['annee_debut_pratique']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (isset($professional['barreau']) && !empty($professional['barreau'])): ?>
                            <p><?php echo htmlspecialchars($professional['barreau']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Diplômes et formation -->
                <div class="profile-section diplomas-section">
                    <h2>Diplômes</h2>
                    <div class="section-content">
                        <div class="diploma-item">
                            <?php if (isset($professional['centre_formation']) && !empty($professional['centre_formation'])): ?>
                            <div class="diploma-text">
                                Diplôme obtenu à <?php echo htmlspecialchars($professional['centre_formation']); ?>
                                <?php if (isset($professional['specialisation_diplome']) && !empty($professional['specialisation_diplome'])): ?>
                                 - Spécialisation: <?php echo htmlspecialchars($professional['specialisation_diplome']); ?>
                                <?php endif; ?>
                                <?php if (isset($professional['annee_diplome']) && !empty($professional['annee_diplome'])): ?>
                                 (<?php echo htmlspecialchars($professional['annee_diplome']); ?>)
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="diploma-text">
                                Information non disponible
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Honoraires -->
            <div id="honoraires" class="tab-content">
                <div class="profile-section fees-section">
                    <h2>Honoraires</h2>
                    <div class="section-content">
                        <div class="presentation-text">
                            <p>Ce professionnel propose d'établir ses honoraires selon les modalités ci-dessous. Ces honoraires sont indicatifs : tout professionnel établira une convention d'honoraires détaillée et adaptée à votre demande.</p>
                        </div>

                        <div class="fee-rates">
                            <?php if (isset($professional['tarif_min']) && isset($professional['tarif_max'])): ?>
                            <div class="fee-rate active">
                                <?php if (isset($professional['type_tarification']) && $professional['type_tarification'] === 'horaire'): ?>
                                    Au taux horaire : <?php echo htmlspecialchars($professional['tarif_min']); ?>€ à <?php echo htmlspecialchars($professional['tarif_max']); ?>€
                                <?php elseif (isset($professional['type_tarification']) && $professional['type_tarification'] === 'forfaitaire'): ?>
                                    Tarif forfaitaire : à partir de <?php echo htmlspecialchars($professional['tarif_min']); ?>€
                                <?php else: ?>
                                    Selon convention : <?php echo htmlspecialchars($professional['tarif_min']); ?>€ à <?php echo htmlspecialchars($professional['tarif_max']); ?>€
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="fee-rate <?php echo (isset($professional['type_tarification']) && $professional['type_tarification'] === 'horaire') ? 'active' : ''; ?>">Taux horaire</div>
                            <div class="fee-rate <?php echo (isset($professional['type_tarification']) && $professional['type_tarification'] === 'forfaitaire') ? 'active' : ''; ?>">Forfaitaire</div>
                            <div class="fee-rate <?php echo (isset($professional['type_tarification']) && $professional['type_tarification'] === 'convention') ? 'active' : ''; ?>">Selon convention</div>
                        </div>

                        <div class="presentation-text">
                            <p><?php echo htmlspecialchars($professional['prenom'] . ' ' . $professional['nom']); ?> vous communiquera une convention d'honoraires précisant les modalités de sa prestation.</p>
                        </div>

                        <h3 class="card-title" style="margin: 15px 0 10px;">Moyens de paiement</h3>
                        <?php if (isset($professional['moyens_paiement']) && !empty($professional['moyens_paiement'])): ?>
                        <div class="payment-methods">
                            <?php 
                            $moyensPaiement = explode(',', $professional['moyens_paiement']);
                            foreach ($moyensPaiement as $moyen): 
                                $moyen = trim($moyen);
                                $icon = 'money-bill'; // Icône par défaut
                                
                                // Définir l'icône en fonction du moyen de paiement
                                if (stripos($moyen, 'carte') !== false) {
                                    $icon = 'credit-card';
                                } elseif (stripos($moyen, 'virement') !== false) {
                                    $icon = 'university';
                                } elseif (stripos($moyen, 'chèque') !== false) {
                                    $icon = 'money-check';
                                } elseif (stripos($moyen, 'espèces') !== false) {
                                    $icon = 'money-bill';
                                }
                            ?>
                            <div class="payment-method">
                                <i class="fas fa-<?php echo $icon; ?> payment-icon"></i>
                                <?php echo htmlspecialchars($moyen); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <i class="fas fa-university payment-icon"></i>
                                Information non disponible
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Section Adresse -->
            <div id="adresse" class="tab-content">
                <div class="profile-section address-section">
                    <h2>Adresse</h2>
                    <div class="section-content">
                        <div class="address-text">
                            <p><?php echo htmlspecialchars($professional['prenom'] . ' ' . $professional['nom']); ?><br>
                            <?php echo nl2br(htmlspecialchars($professional['adresse_cabinet'])); ?><br>
                            <?php echo htmlspecialchars($professional['departement']); ?></p>
                        </div>
                        
                        <!-- Carte Google Maps si disponible -->
                        <?php if (isset($professional['localisation_map']) && !empty($professional['localisation_map'])): ?>
                        <div class="map-container">
                            <?php echo $professional['localisation_map']; ?>
                        </div>
                        <?php else: ?>
                        <div class="map-container">
                            <img src="/LVDPA/public/assets/img/map-placeholder.jpg" alt="Emplacement sur la carte">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Section Administration -->
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
<div id="administration" class="tab-content">
    <h2>Informations administratives</h2>
    <div class="section-content">
        <?php if (isset($signalementInfo) && $signalementInfo): ?>
        <div class="signalement-info">
            <h3>Signalement actif</h3>
            <p>Raison: <?php echo htmlspecialchars($signalementInfo['raison']); ?></p>
            <p>Signalé le: <?php echo date('d/m/Y H:i', strtotime($signalementInfo['date_signalement'])); ?></p>
            <p>Par: <?php echo htmlspecialchars($signalementInfo['signaleur']); ?></p>
            
            <div class="admin-actions">
                <button class="action-btn approve" data-id="<?php echo $signalementInfo['id']; ?>">Approuver</button>
                <button class="action-btn reject" data-id="<?php echo $signalementInfo['id']; ?>">Rejeter</button>
                <button class="action-btn ban" data-id="<?php echo $signalementInfo['id']; ?>">Bannir l'utilisateur</button>
            </div>
        </div>
        <?php else: ?>
        <p>Aucun signalement actif pour cet utilisateur.</p>
        <?php endif; ?>
        
        <!-- Statistiques d'administration -->
        <div class="admin-stats">
            <h3>Statistiques</h3>
            <p>Nombre total de signalements: <?php echo isset($totalSignalements) ? $totalSignalements : 0; ?></p>
            <p>Nombre de posts: <?php echo isset($postsCount) ? $postsCount : 0; ?></p>
            <p>Dernière connexion: <?php echo isset($lastLogin) && $lastLogin ? date('d/m/Y H:i', strtotime($lastLogin)) : 'Inconnue'; ?></p>
        </div>
        
        <!-- Historique des signalements -->
        <?php if (isset($signalementHistory) && !empty($signalementHistory)): ?>
        <div class="signalement-history">
            <h3>Historique des signalements</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Raison</th>
                        <th>Signalé par</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($signalementHistory as $history): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($history['date_signalement'])); ?></td>
                        <td><?php echo htmlspecialchars($history['raison']); ?></td>
                        <td><?php echo htmlspecialchars($history['signaleur']); ?></td>
                        <td><?php echo $history['est_traite'] ? 'Traité' : 'Non résolu'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
        </div>
    </div>
</div>

<!-- Inclusion des fichiers CSS et JS -->
<link rel="stylesheet" href="/LVDPA/public/assets/css/professional_profile.css">
<script src="/LVDPA/public/assets/js/professional_profile.js"></script>