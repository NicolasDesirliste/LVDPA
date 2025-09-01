<div class="new-post-container">
    <h2>Créer un nouveau sujet de discussion</h2>
    
    <?php if (isset($this->data['errors']) && !empty($this->data['errors'])): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($this->data['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form id="newPostForm" action="/LVDPA/index.php?page=create_post" method="POST">
        <div class="form-group">
            <label for="titre">Titre du sujet</label>
            <input type="text" id="titre" name="titre" value="<?php echo isset($this->data['formData']['titre']) ? htmlspecialchars($this->data['formData']['titre']) : ''; ?>" required>
            <small>Entre 5 et 255 caractères</small>
        </div>
        
        <div class="form-group">
            <label for="categorie">Catégorie</label>
            <select id="categorie" name="categorie" required>
                <option value="">Sélectionnez une catégorie</option>
                <?php foreach ($this->data['categories'] as $key => $value): ?>
                    <option value="<?php echo $key; ?>" <?php echo (isset($this->data['formData']['categorie']) && $this->data['formData']['categorie'] === $key) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($value); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="contenu">Contenu</label>
            <textarea id="contenu" name="contenu" rows="10" required><?php echo isset($this->data['formData']['contenu']) ? htmlspecialchars($this->data['formData']['contenu']) : ''; ?></textarea>
            <small>Minimum 20 caractères</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Créer le sujet</button>
            <a href="/LVDPA/index.php?page=forumpage" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPostForm = document.getElementById('newPostForm');
    
    newPostForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation des données
        const titre = document.getElementById('titre').value.trim();
        const categorie = document.getElementById('categorie').value;
        const contenu = document.getElementById('contenu').value.trim();
        
        let errors = [];
        
        if (!titre) {
            errors.push("Le titre est obligatoire.");
        } else if (titre.length < 5 || titre.length > 255) {
            errors.push("Le titre doit contenir entre 5 et 255 caractères.");
        }
        
        if (!categorie) {
            errors.push("Veuillez sélectionner une catégorie.");
        }
        
        if (!contenu) {
            errors.push("Le contenu est obligatoire.");
        } else if (contenu.length < 20) {
            errors.push("Le contenu doit contenir au moins 20 caractères.");
        }
        
        if (errors.length > 0) {
            // Afficher les erreurs
            let errorHtml = '<div class="error-messages"><ul>';
            errors.forEach(function(error) {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul></div>';
            
            // Trouver ou créer le conteneur d'erreurs
            let errorContainer = document.querySelector('.error-messages');
            if (!errorContainer) {
                newPostForm.insertAdjacentHTML('beforebegin', errorHtml);
            } else {
                errorContainer.innerHTML = '<ul>' + errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
            }
            
            return;
        }
        
        // Si tout est valide, envoyer le formulaire en AJAX
        const formData = new FormData(this);
        
        fetch('/LVDPA/index.php?page=create_post&ajax=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rediriger vers le post créé
                window.location.href = data.redirect;
            } else {
                // Afficher les erreurs
                let errorHtml = '<div class="error-messages"><ul>';
                if (data.errors) {
                    data.errors.forEach(function(error) {
                        errorHtml += '<li>' + error + '</li>';
                    });
                } else if (data.message) {
                    errorHtml += '<li>' + data.message + '</li>';
                } else {
                    errorHtml += '<li>Une erreur est survenue lors de la création du sujet.</li>';
                }
                errorHtml += '</ul></div>';
                
                // Trouver ou créer le conteneur d'erreurs
                let errorContainer = document.querySelector('.error-messages');
                if (!errorContainer) {
                    newPostForm.insertAdjacentHTML('beforebegin', errorHtml);
                } else {
                    errorContainer.innerHTML = '<ul>' + (data.errors ? data.errors.map(e => '<li>' + e + '</li>').join('') : '<li>' + data.message + '</li>') + '</ul>';
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la communication avec le serveur.');
        });
    });
});
</script>

<link rel="stylesheet" href="/LVDPA/public/assets/css/new_post.css">