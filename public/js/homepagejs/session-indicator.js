// public/js/homepagejs/session-indicator.js
/**
 * Indicateur de session utilisateur
 * Responsabilité UNIQUE : Mettre à jour le type d'utilisateur dans le footer
 */

(function() {
    'use strict';
    
    // Sélecteur de l'élément à mettre à jour
    const USER_TYPE_SELECTOR = '.footer-section .footer-content';
    
    // Fonction pour mettre à jour le type d'utilisateur
    function updateUserType(userType) {
        const element = document.querySelector(USER_TYPE_SELECTOR);
        if (!element) return;
        
        // Mettre à jour le texte
        element.textContent = userType;
        
        // Animation visuelle pour attirer l'attention
        animateUpdate(element);
        
        // Optionnel : Changer la classe CSS selon le type
        updateUserTypeClass(element, userType);
    }
    
    // Animation lors de la mise à jour
    function animateUpdate(element) {
        element.style.transition = 'all 0.3s ease';
        element.style.transform = 'scale(1.1)';
        element.style.color = '#37afe5'; // Couleur temporaire
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
            element.style.color = ''; // Retour à la couleur par défaut
        }, 300);
    }
    
    // Mettre à jour la classe CSS selon le type d'utilisateur
    function updateUserTypeClass(element, userType) {
        // Retirer toutes les classes de type
        element.classList.remove('user-visitor', 'user-particulier', 'user-avocat', 'user-psychologue', 'user-mediateur');
        
        // Ajouter la classe correspondante
        const className = 'user-' + userType.toLowerCase();
        element.classList.add(className);
    }
    
    // Écouter l'événement de connexion
    window.addEventListener('userLoggedIn', function(e) {
        if (e.detail && e.detail.userType) {
            updateUserType(e.detail.userType);
        }
    });
    
    // Écouter l'événement de déconnexion
    window.addEventListener('userLoggedOut', function() {
        updateUserType('Visiteur');
    });
    
    // Vérifier périodiquement le statut (optionnel)
    // Utile si la session expire côté serveur
    function checkSessionStatus() {
        fetch('/LVDPA/api/session-status', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.userType) {
                const currentType = document.querySelector(USER_TYPE_SELECTOR)?.textContent;
                if (currentType !== data.userType) {
                    updateUserType(data.userType);
                }
            }
        })
        .catch(error => {
            console.error('Erreur de vérification de session:', error);
        });
    }
    
    // Activer la vérification périodique (toutes les 5 minutes)
    // Décommenter pour activer
    // setInterval(checkSessionStatus, 5 * 60 * 1000);
    
    // Exposer les fonctions publiques
    window.SessionIndicator = {
        update: updateUserType,
        checkStatus: checkSessionStatus
    };
    
})();