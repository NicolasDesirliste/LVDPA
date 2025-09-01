<?php
// Récupérer les posts depuis la base de données
$posts = [];
if (isset($data['posts'])) {
    $posts = $data['posts'];
} else {
    // Données de test si aucun post n'est disponible
    $posts = [
        [
            'id' => 1,
            'utilisateur_id' => 1,
            'pseudo' => 'JeanDupont',
            'titre' => 'Conseils pour une première audience',
            'contenu' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
            'date_creation' => '2025-05-09 14:30:00',
            'categorie' => 'general'
        ],
        [
            'id' => 2,
            'utilisateur_id' => 2,
            'pseudo' => 'MarieMartin',
            'titre' => 'Comment préparer son dossier juridique',
            'contenu' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.',
            'date_creation' => '2025-05-08 09:15:00',
            'categorie' => 'avocats'
        ],
        [
            'id' => 3,
            'utilisateur_id' => 3,
            'pseudo' => 'PierreDurand',
            'titre' => 'Gestion émotionnelle pendant les procédures',
            'contenu' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.',
            'date_creation' => '2025-05-07 16:45:00',
            'categorie' => 'psychologues'
        ],
        
    ];
}
?>

<!-- Liste des posts -->
<div class="post-list">
    <?php foreach ($posts as $post): ?>
        <div class="post-item" data-post-id="<?php echo $post['id']; ?>">
            <div class="post-metadata">
                <span class="post-author">Post de: <?php echo htmlspecialchars($post['pseudo']); ?></span>
                <?php 
                $date = new DateTime($post['date_creation']);
                $dateFormatted = $date->format('d/m/Y');
                $timeFormatted = $date->format('H:i:s');
                ?>
                <span class="post-date">Le: <?php echo $dateFormatted; ?></span>
                <span class="post-time">à: <?php echo $timeFormatted; ?></span>
                <span class="post-marker">Marqueur: <?php echo ucfirst(htmlspecialchars($post['categorie'])); ?></span>
            </div>
            <div class="post-title">
                <?php echo htmlspecialchars($post['titre']); ?>
            </div>
            <div class="post-preview">
                <?php 
                // Limiter le contenu à 200 caractères pour la prévisualisation
                $preview = substr($post['contenu'], 0, 200);
                if (strlen($post['contenu']) > 200) {
                    $preview .= '...';
                }
                echo nl2br(htmlspecialchars($preview)); 
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation vers la page de détail du post
    const postItems = document.querySelectorAll('.post-item');
    postItems.forEach(item => {
        item.addEventListener('click', function() {
            const postId = this.dataset.postId;
            window.location.href = `/LVDPA/index.php?page=post_detail&id=${postId}`;
        });
    });
});
</script>

<link rel="stylesheet" href="/LVDPA/public/assets/css/forumpage.css">