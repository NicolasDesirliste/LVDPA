// public/js/homepage.js/sidebar-updater.js
/**
 * Mise à jour dynamique de la sidebar
 * Responsabilité: Recharger la sidebar lors des changements de session
 */

(function() {
    'use strict';
    
    /**
     * Récupère les menus depuis l'API et met à jour la sidebar
     */
    function updateSidebar() {
        fetch('/LVDPA/api/sidebar-menus', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.menus) {
                renderSidebar(data.menus);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour de la sidebar:', error);
        });
    }
    
    /**
     * Génère le HTML de la sidebar à partir des données
     * @param {Array} menus 
     */
    function renderSidebar(menus) {
        const sidebar = document.querySelector('.sidebar .menu-list');
        if (!sidebar) return;
        
        // Vider la sidebar actuelle
        sidebar.innerHTML = '';
        
        // Générer le nouveau contenu
        menus.forEach(menu => {
            const menuItem = createMenuItem(menu);
            sidebar.appendChild(menuItem);
        });
        
        // Animation pour indiquer la mise à jour
        animateSidebarUpdate();
    }
    
    /**
     * Crée un élément de menu
     * @param {Object} menu 
     * @returns {HTMLElement}
     */
    function createMenuItem(menu) {
        const li = document.createElement('li');
        li.className = 'menu-item';
        
        // Titre du menu
        li.innerHTML = `
            ${menu.title}
            <div class="sidebar-tooltip">
                <div class="tooltips-header">
                    <h6>${menu.tooltip_header}</h6>
                </div>
            </div>
        `;
        
        // Container pour les liens
        const tooltip = li.querySelector('.sidebar-tooltip');
        
        // Ajouter les liens
        menu.links.forEach(link => {
            const a = createLink(link);
            tooltip.appendChild(a);
        });
        
        return li;
    }
    
    /**
     * Crée un lien
     * @param {Object} link 
     * @returns {HTMLElement}
     */
    function createLink(link) {
        const a = document.createElement('a');
        a.href = link.href;
        a.className = 'ff-button';
        a.textContent = link.text;
        
        // Ajouter les attributs optionnels
        if (link.spa) {
            a.setAttribute('data-spa-link', '');
        }
        
        if (link.id) {
            a.id = link.id;
        }
        
        if (link.class) {
            a.className += ' ' + link.class;
        }
        
        return a;
    }
    
    /**
     * Animation visuelle lors de la mise à jour
     */
    function animateSidebarUpdate() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        
        // Ajouter une classe temporaire pour l'animation
        sidebar.classList.add('sidebar-updating');
        
        setTimeout(() => {
            sidebar.classList.remove('sidebar-updating');
        }, 300);
    }
    
    /**
     * Écouter les événements de changement de session
     */
    window.addEventListener('userLoggedIn', function() {
        console.log('Mise à jour de la sidebar après connexion');
        // Attendre un peu pour que la session soit bien mise à jour côté serveur
        setTimeout(updateSidebar, 100);
    });
    
    window.addEventListener('userLoggedOut', function() {
        console.log('Mise à jour de la sidebar après déconnexion');
        setTimeout(updateSidebar, 100);
    });
    
    // Exposer la fonction pour usage manuel si nécessaire
    window.SidebarUpdater = {
        update: updateSidebar
    };
    
})();