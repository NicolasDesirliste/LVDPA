<?php
// Page d'édition du profil professionnel 12/05/2025
?>
<div class="profile-edit-container">
    <h2>Éditer mon profil professionnel</h2>
    
    <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($data['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form id="edit-profile-form" action="/LVDPA/index.php?page=update_profile_professionnel" method="POST" enctype="multipart/form-data">
        <h3 class="section-title">Informations personnelles</h3>
        
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo isset($this->data['formData']['nom']) ? htmlspecialchars($this->data['formData']['nom']) : (isset($this->data['profile']['nom']) ? htmlspecialchars($this->data['profile']['nom']) : ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo isset($this->data['formData']['prenom']) ? htmlspecialchars($this->data['formData']['prenom']) : (isset($this->data['profile']['prenom']) ? htmlspecialchars($this->data['profile']['prenom']) : ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="telephone">Numéro de téléphone:</label>
            <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" value="<?php echo isset($this->data['formData']['telephone']) ? htmlspecialchars($this->data['formData']['telephone']) : (isset($this->data['profile']['telephone']) ? htmlspecialchars($this->data['profile']['telephone']) : ''); ?>" required placeholder="0600000000">
            <small>Format: 10 chiffres sans espaces ni caractères spéciaux</small>
        </div>
        
        <div class="form-group">
            <label for="type_professionnel">Type de professionnel:</label>
            <select id="type_professionnel" name="type_professionnel">
                <option value="">Sélectionnez votre type</option>
                <option value="avocat" <?php echo (isset($this->data['profile']['type_professionnel']) && $this->data['profile']['type_professionnel'] === 'avocat') ? 'selected' : ''; ?>>Avocat</option>
                <option value="psychologue" <?php echo (isset($this->data['profile']['type_professionnel']) && $this->data['profile']['type_professionnel'] === 'psychologue') ? 'selected' : ''; ?>>Psychologue</option>
                <option value="mediateur" <?php echo (isset($this->data['profile']['type_professionnel']) && $this->data['profile']['type_professionnel'] === 'mediateur') ? 'selected' : ''; ?>>Médiateur</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="photo">Photo de profil:</label>
            <?php if (isset($this->data['profile']['photo']) && !empty($this->data['profile']['photo'])): ?>
                <div class="current-photo">
                    <img src="<?php echo htmlspecialchars($this->data['profile']['photo']); ?>" alt="Photo de profil actuelle" width="150">
                    <p>Photo actuelle</p>
                </div>
            <?php endif; ?>
            <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif">
            <small>Formats acceptés: JPEG, PNG, GIF. Taille maximale: 2 Mo.</small>
        </div>
        
        <h3 class="section-title">Informations professionnelles</h3>
        
        <div class="form-group">
            <label for="adresse_cabinet">Adresse du cabinet:</label>
            <textarea id="adresse_cabinet" name="adresse_cabinet" rows="3" required><?php echo isset($this->data['formData']['adresse_cabinet']) ? htmlspecialchars($this->data['formData']['adresse_cabinet']) : (isset($this->data['profile']['adresse_cabinet']) ? htmlspecialchars($this->data['profile']['adresse_cabinet']) : ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="barreau">Barreau/Ordre professionnel:</label>
            <input type="text" id="barreau" name="barreau" value="<?php echo isset($this->data['formData']['barreau']) ? htmlspecialchars($this->data['formData']['barreau']) : (isset($this->data['profile']['barreau']) ? htmlspecialchars($this->data['profile']['barreau']) : ''); ?>">
            <small>Pour les avocats uniquement</small>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="annee_debut_pratique">Année de début de pratique:</label>
                <input type="number" id="annee_debut_pratique" name="annee_debut_pratique" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo isset($this->data['formData']['annee_debut_pratique']) ? htmlspecialchars($this->data['formData']['annee_debut_pratique']) : (isset($this->data['profile']['annee_debut_pratique']) ? htmlspecialchars($this->data['profile']['annee_debut_pratique']) : ''); ?>">
            </div>
            
            <div class="form-group half">
                <label for="accepte_aide_pro_deo">Acceptez-vous l'aide pro deo ?</label>
                <div class="checkbox-container">
                    <input type="checkbox" id="accepte_aide_pro_deo" name="accepte_aide_pro_deo" value="1" <?php echo (isset($this->data['formData']['accepte_aide_pro_deo']) && $this->data['formData']['accepte_aide_pro_deo']) || (isset($this->data['profile']['accepte_aide_pro_deo']) && $this->data['profile']['accepte_aide_pro_deo']) ? 'checked' : ''; ?>>
                    <label for="accepte_aide_pro_deo" class="checkbox-label">Oui, j'accepte</label>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="repond_rapidement">Répondez-vous rapidement aux demandes ?</label>
            <div class="checkbox-container">
                <input type="checkbox" id="repond_rapidement" name="repond_rapidement" value="1" <?php echo (isset($this->data['formData']['repond_rapidement']) && $this->data['formData']['repond_rapidement']) || (isset($this->data['profile']['repond_rapidement']) && $this->data['profile']['repond_rapidement']) ? 'checked' : ''; ?>>
                <label for="repond_rapidement" class="checkbox-label">Oui, je réponds généralement en moins de 24h</label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="numero_diplome">Numéro de diplôme:</label>
            <input type="text" id="numero_diplome" name="numero_diplome" value="<?php echo isset($this->data['formData']['numero_diplome']) ? htmlspecialchars($this->data['formData']['numero_diplome']) : (isset($this->data['profile']['numero_diplome']) ? htmlspecialchars($this->data['profile']['numero_diplome']) : ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="centre_formation">Centre de formation / École:</label>
            <input type="text" id="centre_formation" name="centre_formation" value="<?php echo isset($this->data['formData']['centre_formation']) ? htmlspecialchars($this->data['formData']['centre_formation']) : (isset($this->data['profile']['centre_formation']) ? htmlspecialchars($this->data['profile']['centre_formation']) : ''); ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="specialisation_diplome">Spécialisation diplôme:</label>
                <input type="text" id="specialisation_diplome" name="specialisation_diplome" value="<?php echo isset($this->data['formData']['specialisation_diplome']) ? htmlspecialchars($this->data['formData']['specialisation_diplome']) : (isset($this->data['profile']['specialisation_diplome']) ? htmlspecialchars($this->data['profile']['specialisation_diplome']) : ''); ?>">
            </div>
            
            <div class="form-group half">
                <label for="annee_diplome">Année d'obtention:</label>
                <input type="number" id="annee_diplome" name="annee_diplome" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo isset($this->data['formData']['annee_diplome']) ? htmlspecialchars($this->data['formData']['annee_diplome']) : (isset($this->data['profile']['annee_diplome']) ? htmlspecialchars($this->data['profile']['annee_diplome']) : ''); ?>">
            </div>
        </div>
        
        <h3 class="section-title">Spécialités et Compétences</h3>
        
        <div class="form-group">
            <label for="domaines">Domaines (séparés par des virgules):</label>
            <input type="text" id="domaines" name="domaines" value="<?php 
                if (isset($this->data['formData']['domaines'])) {
                    echo htmlspecialchars($this->data['formData']['domaines']);
                } elseif (isset($this->data['professional_domains']) && !empty($this->data['professional_domains'])) {
                    $domains = array_map(function($domain) { return $domain['domaine']; }, $this->data['professional_domains']);
                    echo htmlspecialchars(implode(', ', $domains));
                }
            ?>">
            <small>Exemple: Droit de la Famille, Droit du Travail</small>
        </div>
        
        <div class="form-group">
            <label for="competences">Compétences (séparées par des virgules):</label>
            <input type="text" id="competences" name="competences" value="<?php 
                if (isset($this->data['formData']['competences'])) {
                    echo htmlspecialchars($this->data['formData']['competences']);
                } elseif (isset($this->data['professional_skills']) && !empty($this->data['professional_skills'])) {
                    $skills = array_map(function($skill) { return $skill['competence']; }, $this->data['professional_skills']);
                    echo htmlspecialchars(implode(', ', $skills));
                }
            ?>">
            <small>Exemple: Divorce, Médiation, Garde d'enfants</small>
        </div>
        
        <h3 class="section-title">Horaires</h3>
        
        <div class="form-group">
            <label>Heures de travail principales:</label>
            <div class="time-range">
                <input type="time" id="heures_debut" name="heures_debut" value="<?php echo isset($this->data['formData']['heures_debut']) ? htmlspecialchars($this->data['formData']['heures_debut']) : (isset($this->data['profile']['heures_debut']) ? htmlspecialchars($this->data['profile']['heures_debut']) : '09:00'); ?>" required>
                <span>à</span>
                <input type="time" id="heures_fin" name="heures_fin" value="<?php echo isset($this->data['formData']['heures_fin']) ? htmlspecialchars($this->data['formData']['heures_fin']) : (isset($this->data['profile']['heures_fin']) ? htmlspecialchars($this->data['profile']['heures_fin']) : '18:00'); ?>" required>
            </div>
        </div>
        
        <div class="hours-editor">
            <?php
            $weekDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            foreach ($weekDays as $index => $day):
                $existingHour = null;
                if (isset($this->data['professional_hours']) && !empty($this->data['professional_hours'])) {
                    foreach ($this->data['professional_hours'] as $hour) {
                        if ($hour['jour_semaine'] === $day) {
                            $existingHour = $hour;
                            break;
                        }
                    }
                }
            ?>
            <div class="day-hours">
                <div class="day-checkbox">
                    <input type="checkbox" id="day_active_<?php echo $index; ?>" name="day_active[<?php echo $index; ?>]" value="1" <?php echo $existingHour ? 'checked' : ''; ?>>
                    <label for="day_active_<?php echo $index; ?>"><?php echo $day; ?></label>
                </div>
                <div class="day-times">
                    <input type="time" id="day_start_<?php echo $index; ?>" name="day_start[<?php echo $index; ?>]" value="<?php echo $existingHour ? htmlspecialchars($existingHour['heure_debut']) : '09:00'; ?>" <?php echo !$existingHour ? 'disabled' : ''; ?>>
                    <span>à</span>
                    <input type="time" id="day_end_<?php echo $index; ?>" name="day_end[<?php echo $index; ?>]" value="<?php echo $existingHour ? htmlspecialchars($existingHour['heure_fin']) : '18:00'; ?>" <?php echo !$existingHour ? 'disabled' : ''; ?>>
                    <input type="hidden" name="day_name[<?php echo $index; ?>]" value="<?php echo $day; ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <h3 class="section-title">Honoraires</h3>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="type_tarification">Type de tarification:</label>
                <select id="type_tarification" name="type_tarification" required>
                    <option value="">Sélectionnez le type</option>
                    <option value="horaire" <?php echo (isset($this->data['profile']['type_tarification']) && $this->data['profile']['type_tarification'] === 'horaire') ? 'selected' : ''; ?>>Taux horaire</option>
                    <option value="forfaitaire" <?php echo (isset($this->data['profile']['type_tarification']) && $this->data['profile']['type_tarification'] === 'forfaitaire') ? 'selected' : ''; ?>>Forfaitaire</option>
                    <option value="convention" <?php echo (isset($this->data['profile']['type_tarification']) && $this->data['profile']['type_tarification'] === 'convention') ? 'selected' : ''; ?>>Selon convention</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="tarif_min">Tarif minimum (€):</label>
                <input type="number" id="tarif_min" name="tarif_min" min="0" step="0.01" value="<?php echo isset($this->data['formData']['tarif_min']) ? htmlspecialchars($this->data['formData']['tarif_min']) : (isset($this->data['profile']['tarif_min']) ? htmlspecialchars($this->data['profile']['tarif_min']) : ''); ?>">
            </div>
            
            <div class="form-group half">
                <label for="tarif_max">Tarif maximum (€):</label>
                <input type="number" id="tarif_max" name="tarif_max" min="0" step="0.01" value="<?php echo isset($this->data['formData']['tarif_max']) ? htmlspecialchars($this->data['formData']['tarif_max']) : (isset($this->data['profile']['tarif_max']) ? htmlspecialchars($this->data['profile']['tarif_max']) : ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="moyens_paiement">Moyens de paiement acceptés (séparés par des virgules):</label>
            <input type="text" id="moyens_paiement" name="moyens_paiement" value="<?php echo isset($this->data['formData']['moyens_paiement']) ? htmlspecialchars($this->data['formData']['moyens_paiement']) : (isset($this->data['profile']['moyens_paiement']) ? htmlspecialchars($this->data['profile']['moyens_paiement']) : ''); ?>">
            <small>Exemple: Virement, Chèque, Espèces, Carte bancaire</small>
        </div>
        
        <h3 class="section-title">Présentation et Adresse</h3>
        
        <div class="form-group">
            <label for="biographie">Biographie / Présentation détaillée:</label>
            <textarea id="biographie" name="biographie" rows="6" required><?php echo isset($this->data['formData']['biographie']) ? htmlspecialchars($this->data['formData']['biographie']) : (isset($this->data['profile']['biographie']) ? htmlspecialchars($this->data['profile']['biographie']) : ''); ?></textarea>
            <small>Présentez votre parcours, vos spécialités et votre approche professionnelle</small>
        </div>
        
        <div class="form-group">
            <label for="site_web">Site web personnel:</label>
            <input type="url" id="site_web" name="site_web" value="<?php echo isset($this->data['formData']['site_web']) ? htmlspecialchars($this->data['formData']['site_web']) : (isset($this->data['profile']['site_web']) ? htmlspecialchars($this->data['profile']['site_web']) : ''); ?>" placeholder="https://www.example.com">
            <small>Format: URL complète avec http:// ou https://</small>
        </div>
        
        <div class="form-group">
            <label for="localisation_map">Localisation Google Maps (code d'intégration iframe):</label>
            <textarea id="localisation_map" name="localisation_map" rows="3"><?php echo isset($this->data['formData']['localisation_map']) ? htmlspecialchars($this->data['formData']['localisation_map']) : (isset($this->data['profile']['localisation_map']) ? htmlspecialchars($this->data['profile']['localisation_map']) : ''); ?></textarea>
            <small>Collez ici le code d'intégration Google Maps de votre cabinet (iframe)</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            <a href="/LVDPA/index.php?page=profil" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="/LVDPA/public/assets/css/profile_edit.css">
<link rel="stylesheet" href="/LVDPA/public/assets/css/edit_profile_professionnel.css">
<script src="/LVDPA/public/assets/js/edit_profile_professionnel.js"></script>