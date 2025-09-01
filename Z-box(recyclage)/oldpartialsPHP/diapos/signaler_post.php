<div class="signal-post-container">
    <h2>Signaler un <?php echo $this->data['contentType']; ?></h2>
    
    <?php if (isset($this->data['errors']) && !empty($this->data['errors'])): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($this->data['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form id="signalForm" action="/LVDPA/index.php?page=signaler_post&id=<?php echo $this->data['contentId']; ?><?php echo $this->data['isResponse'] ? '&response=1' : ''; ?>" method="POST">
        <div class="form-group">
            <label for="raison">Raison du signalement</label>
            <select id="raison" name="raison" required>
                <option value="">Sélectionnez une raison</option>
                <option value="contenu_inapproprie">Contenu inapproprié</option>
                <option value="contenu_offensant">Contenu offensant ou insultant</option>
                <option value="contenu_illegal">Contenu illégal</option>
                <option value="spam">Spam</option>
                <option value="autre">Autre</option>
            </select>
        </div>
        
        <div class="form-group" id="detailsContainer" style="display:none;">
            <label for="details">Précisions</label>
            <textarea id="details" name="details" rows="5"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Envoyer le signalement</button>
            <a href="/LVDPA/index.php?page=post_detail&id=<?php echo $this->data['redirectId']; ?>" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const raisonSelect = document.getElementById('raison');
    const detailsContainer = document.getElementById('detailsContainer');
    
    raisonSelect.addEventListener('change', function() {
        if (this.value === 'autre') {
            detailsContainer.style.display = 'block';
        } else {
            detailsContainer.style.display = 'none';
        }
    });
});
</script>

<link rel="stylesheet" href="/LVDPA/public/assets/css/signal_post.css">