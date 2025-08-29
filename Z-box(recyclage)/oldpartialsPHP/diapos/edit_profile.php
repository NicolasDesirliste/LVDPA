<?php
// Ensure this template is only used within a class context
if (!isset($this) || !is_object($this)) {
    die('This template must be included within a class context');
}

// Safely get data from $this
$errors = isset($this->data['errors']) ? $this->data['errors'] : [];
$formData = isset($this->data['formData']) ? $this->data['formData'] : [];
$profile = isset($this->data['profile']) ? $this->data['profile'] : [];
?>
<?php if (!empty($errors)): ?>
    <div class="error-messages">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
            <input type="text" id="nom" name="nom" value="<?php echo isset($formData['nom']) ? htmlspecialchars($formData['nom']) : (isset($profile['nom']) ? htmlspecialchars($profile['nom']) : ''); ?>" required>
    
    <form id="edit-profile-form" action="/LVDPA/index.php?page=update_profile_particulier" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo isset($formData['prenom']) ? htmlspecialchars($formData['prenom']) : (isset($profile['prenom']) ? htmlspecialchars($profile['prenom']) : ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom:</label>
            <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" value="<?php echo isset($formData['telephone']) ? htmlspecialchars($formData['telephone']) : (isset($profile['telephone']) ? htmlspecialchars($profile['telephone']) : ''); ?>" placeholder="0600000000">
        </div>
        
        <div class="form-group">
            <label for="telephone">Numéro de téléphone:</label>
            <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" value="<?php echo isset($this->data['formData']['telephone']) ? htmlspecialchars($this->data['formData']['telephone']) : (isset($this->data['profile']['telephone']) ? htmlspecialchars($this->data['profile']['telephone']) : ''); ?>" placeholder="0600000000">
            <?php if (isset($profile['photo']) && !empty($profile['photo'])): ?>
                <div class="current-photo">
                    <img src="<?php echo htmlspecialchars($profile['photo']); ?>" alt="Photo de profil actuelle" width="150">
                    <p>Photo actuelle</p>
                </div>
            <?php elseif (isset($this->data['profile']['photo']) && !empty($this->data['profile']['photo'])): ?>
                <div class="current-photo">
                    <img src="<?php echo htmlspecialchars($this->data['profile']['photo']); ?>" alt="Photo de profil actuelle" width="150">
                    <p>Photo actuelle</p>
                </div>
            <?php endif; ?>
            <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif">
            <small>Formats acceptés: JPEG, PNG, GIF. Taille maximale: 2 Mo.</small>
                <input type="time" id="preference_contact_debut" name="preference_contact_debut" value="<?php echo isset($formData['preference_contact_debut']) ? htmlspecialchars($formData['preference_contact_debut']) : (isset($profile['preference_contact_debut']) ? htmlspecialchars($profile['preference_contact_debut']) : '09:00'); ?>">
        
                <input type="time" id="preference_contact_fin" name="preference_contact_fin" value="<?php echo isset($formData['preference_contact_fin']) ? htmlspecialchars($formData['preference_contact_fin']) : (isset($profile['preference_contact_fin']) ? htmlspecialchars($profile['preference_contact_fin']) : '18:00'); ?>">
            <label>Préférence de contact (entre quelle heure et quelle heure):</label>
            <div class="time-range">
                <input type="time" id="preference_contact_debut" name="preference_contact_debut" value="<?php echo isset($this->data['formData']['preference_contact_debut']) ? htmlspecialchars($this->data['formData']['preference_contact_debut']) : (isset($this->data['profile']['preference_contact_debut']) ? htmlspecialchars($this->data['profile']['preference_contact_debut']) : '09:00'); ?>">
                <span>à</span>
                <input type="time" id="preference_contact_fin" name="preference_contact_fin" value="<?php echo isset($this->data['formData']['preference_contact_fin']) ? htmlspecialchars($this->data['formData']['preference_contact_fin']) : (isset($this->data['profile']['preference_contact_fin']) ? htmlspecialchars($this->data['profile']['preference_contact_fin']) : '18:00'); ?>">
            <textarea id="biographie" name="biographie" rows="6"><?php echo isset($formData['biographie']) ? htmlspecialchars($formData['biographie']) : (isset($profile['biographie']) ? htmlspecialchars($profile['biographie']) : ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="biographie">Biographie:</label>
            <textarea id="biographie" name="biographie" rows="6"><?php echo isset($this->data['formData']['biographie']) ? htmlspecialchars($this->data['formData']['biographie']) : (isset($this->data['profile']['biographie']) ? htmlspecialchars($this->data['profile']['biographie']) : ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            <a href="/LVDPA/index.php?page=profil" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="/LVDPA/public/assets/css/profile_edit.css">
<script src="/LVDPA/public/assets/js/profile_edit.js"></script>