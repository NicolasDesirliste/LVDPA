<?php
// Récupérer l'ID du post demandé
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les détails du post depuis la base de données
$post = [];
$responses = [];

if (isset($data['post'])) {
    $post = $data['post'];
    $responses = $data['responses'] ?? [];
} else {
    // Données de test si aucun post n'est disponible
    $post = [
        'id' => 1,
        'utilisateur_id' => 1,
        'pseudo' => 'JeanDupont',
        'type' => 'Particulier',
        'titre' => 'Conseils pour une première audience',
        'contenu' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'date_creation' => '2025-05-09 14:30:00',
        'categorie' => 'general',
        'photo' => '/LVDPA/public/assets/img/no-profile-pic.png' // Photo par défaut
    ];
    
    // Réponses de test
    $responses = [
        [
            'id' => 101,
            'post_id' => 1,
            'utilisateur_id' => 2,
            'pseudo' => 'MarieMartin',
            'type' => 'avocat',
            'contenu' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt',
            'date_creation' => '2025-05-09 15:45:00',
            'photo' => '/LVDPA/public/assets/img/no-profile-pic.png'
        ],
        [
            'id' => 102,
            'post_id' => 1,
            'utilisateur_id' => 3,
            'pseudo' => 'PierreDurand',
            'type' => 'psychologue',
            'contenu' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.',
            'date_creation' => '2025-05-09 16:20:00',
            'photo' => '/LVDPA/public/assets/img/no-profile-pic.png'
        ]
    ];
}

// Formater la date et l'heure
$dateTime = new DateTime($post['date_creation']);
$formattedTime = $dateTime->format('H:i:s');
?>

<div class="post-detail-container">
    <!-- Post principal -->
    <div class="post-main">
        <div class="post-header">
            <h2 class="post-title"><?php echo htmlspecialchars($post['titre']); ?></h2>
            <div class="post-time">à <?php echo $formattedTime; ?></div>
            <div class="back-button" title="Retour au forum">
                <span class="back-arrow">←</span>
                <div class="tooltip">
                    <a href="/LVDPA/index.php?page=forumpage" class="tooltip-button">Retour au forum</a>
                    <a href="/LVDPA/index.php?page=signaler_post&id=<?php echo $post['id']; ?>" class="tooltip-button">Signaler ce post</a>
                </div>
            </div>
        </div>
        
        <div class="post-content-wrapper">
            <div class="user-info">
                <div class="user-avatar">
                    <img src="<?php echo $post['photo']; ?>" alt="Avatar de <?php echo htmlspecialchars($post['pseudo']); ?>">
                </div>
                <div class="user-details">
                    <div class="user-pseudo"><?php echo htmlspecialchars($post['pseudo']); ?></div>
                    <div class="user-type"><?php echo htmlspecialchars($post['type']); ?></div>
                    <a href="/LVDPA/index.php?page=professional_profile&id=<?php echo $post['utilisateur_id']; ?>" class="profile-link">Mon profil</a>
                </div>
            </div>
            
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['contenu'])); ?>
            </div>
        </div>
        
        <div class="post-footer">
            <div class="responses-count">Réponses: <?php echo count($responses); ?></div>
        </div>
    </div>
    
    <!-- Formulaire de réponse (état initial compact) -->
    <div class="reply-form-container" id="replyFormContainer">
        <div class="reply-form-compact" id="replyFormCompact">
            Rejoindre la conversation ?
        </div>
        
        <div class="reply-form-expanded" id="replyFormExpanded">
            <textarea id="replyContent" name="replyContent" placeholder="Écrivez votre réponse ici..."></textarea>
            <div class="reply-form-actions">
                <button id="cancelReply" class="cancel-button">Annuler</button>
                <button id="submitReply" class="submit-button">Répondre</button>
            </div>
        </div>
    </div>
    
    <!-- Liste des réponses -->
    <div class="responses-list">
        <?php foreach ($responses as $response): ?>
            <div class="response-item">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="<?php echo $response['photo']; ?>" alt="Avatar de <?php echo htmlspecialchars($response['pseudo']); ?>">
                    </div>
                    <div class="user-details">
                        <div class="user-pseudo"><?php echo htmlspecialchars($response['pseudo']); ?></div>
                        <div class="user-type"><?php echo htmlspecialchars($response['type']); ?></div>
                        <a href="/LVDPA/index.php?page=professional_profile&id=<?php echo $response['utilisateur_id']; ?>" class="profile-link">Mon profil</a>
                    </div>
                </div>
                
                <div class="response-content">
                    <?php echo nl2br(htmlspecialchars($response['contenu'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyFormCompact = document.getElementById('replyFormCompact');
    const replyFormExpanded = document.getElementById('replyFormExpanded');
    const cancelReplyButton = document.getElementById('cancelReply');
    const submitReplyButton = document.getElementById('submitReply');
    const replyContent = document.getElementById('replyContent');
    
    // Afficher le formulaire de réponse complet au clic sur la version compacte
    replyFormCompact.addEventListener('click', function() {
        replyFormCompact.style.display = 'none';
        replyFormExpanded.style.display = 'block';
        replyContent.focus();
    });
    
    // Annuler la réponse et revenir à la version compacte
    cancelReplyButton.addEventListener('click', function() {
        replyFormExpanded.style.display = 'none';
        replyFormCompact.style.display = 'block';
        replyContent.value = '';
    });
    
    // Soumettre la réponse
    submitReplyButton.addEventListener('click', function() {
        const content = replyContent.value.trim();
        
        if (content) {
            // Afficher un indicateur de chargement
            submitReplyButton.disabled = true;
            submitReplyButton.textContent = 'Envoi en cours...';
            
            // Préparer les données du formulaire
            const formData = new FormData();
            formData.append('post_id', <?php echo $postId; ?>);
            formData.append('contenu', content);
            
            // Envoyer la réponse au serveur via AJAX
            fetch('/LVDPA/index.php?page=add_response&ajax=1', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Réinitialiser le formulaire
                    replyFormExpanded.style.display = 'none';
                    replyFormCompact.style.display = 'block';
                    replyContent.value = '';
                    
                    // Ajouter la nouvelle réponse à la liste sans recharger la page
                    const responsesList = document.querySelector('.responses-list');
                    
                    // Créer l'élément HTML pour la nouvelle réponse
                    const newResponse = document.createElement('div');
                    newResponse.className = 'response-item';
                    newResponse.innerHTML = `
                        <div class="user-info">
                            <div class="user-avatar">
                                <img src="${data.response.photo}" alt="Avatar de ${data.response.pseudo}">
                            </div>
                            <div class="user-details">
                                <div class="user-pseudo">${data.response.pseudo}</div>
                                <div class="user-type">${data.response.type}</div>
                                <a href="/LVDPA/index.php?page=professional_profile&id=${data.response.utilisateur_id}" class="profile-link">Mon profil</a>
                            </div>
                        </div>
                        
                        <div class="response-content">
                            ${data.response.contenu.replace(/\n/g, '<br>')}
                        </div>
                    `;
                    
                    // Ajouter la nouvelle réponse au début de la liste
                    responsesList.prepend(newResponse);
                    
                    // Mettre à jour le compteur de réponses
                    const responsesCount = document.querySelector('.responses-count');
                    const currentCount = parseInt(responsesCount.textContent.match(/\d+/)[0]);
                    responsesCount.textContent = `Réponses: ${currentCount + 1}`;
                    
                    // Afficher un message de succès
                    const flashMessage = document.createElement('div');
                    flashMessage.className = 'flash-message success';
                    flashMessage.textContent = data.message;
                    document.querySelector('.post-detail-container').prepend(flashMessage);
                    
                    // Supprimer le message après quelques secondes
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 5000);
                } else {
                    // Afficher les erreurs
                    alert(data.message || 'Une erreur est survenue lors de l\'envoi de la réponse.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la communication avec le serveur.');
            })
            .finally(() => {
                // Réactiver le bouton
                submitReplyButton.disabled = false;
                submitReplyButton.textContent = 'Répondre';
            });
        } else {
            alert('Veuillez écrire une réponse avant de soumettre.');
        }
    });
});
</script>

<link rel="stylesheet" href="/LVDPA/public/assets/css/post_detail.css">