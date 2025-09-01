<div id="searchModal" class="modal">
    <div class="modal-container">
        <div class="contact-form-outer">
            <div class="form-header">
                <h2>Recherche de Professionnels</h2>
                <a href="/LVDPA/index.php?page=accueil" class="close-form-btn">×</a>
            </div>
            
            <div id="info-text">
                <p>Trouvez rapidement un professionnel qualifié pour vous accompagner dans vos démarches • Seuls les professionnels inscrits et vérifiés sont présentés • Un accompagnement adapté à vos besoins</p>
            </div>
            
            <div class="form-content">
                <div class="form-container">
                    <div class="infotext-static">
                        <p>Sélectionnez votre département et le type de professionnel dont vous avez besoin pour obtenir une liste des personnes disponibles près de chez vous.</p>
                    </div>
                    
                    <form id="searchForm" action="/LVDPA/index.php?page=search_results" method="GET">
                        <input type="hidden" name="page" value="search_results">
                        
                        <select name="departement" id="departementSelect" required>
                            <option value="">Sélectionnez le département du type de professionnel recherché</option>
                            <!-- Les départements sont chargés dynamiquement par JS -->
                        </select>
                    
                        <select name="profession" id="professionSelect" required>
                            <option value="">Choisissez le type de professionnel recherché</option>
                            <option value="all">Tous les Professionnels confondus</option>
                            <option value="avocat">Avocats</option>
                            <option value="psychologue">Psychologues</option>
                            <option value="mediateur">Médiateurs</option>
                        </select>
                        
                        <button type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/LVDPA/public/assets/js/searchform.js"></script>
<link rel="stylesheet" href="/LVDPA/public/assets/css/searchform.css">