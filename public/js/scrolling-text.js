// public/js/scrolling-text.js
/**
 * Gestion du défilement du titre
 * Responsabilité UNIQUE : Faire défiler le texte dans #main-flying-message
 */

(function() {
    'use strict';
    
    let currentAnimation = null;
    
    // Fonction pour faire défiler le texte
    function startScrolling(element) {
        // Arrêter l'animation précédente si elle existe
        if (currentAnimation) {
            cancelAnimationFrame(currentAnimation);
        }
        
        const originalText = element.textContent;
        
        // Créer un conteneur pour le défilement
        element.innerHTML = `<span>${originalText}</span>`;
        const textSpan = element.querySelector('span');
        
        // Styles nécessaires
        element.style.overflow = 'hidden';
        element.style.whiteSpace = 'nowrap';
        textSpan.style.display = 'inline-block';
        textSpan.style.paddingLeft = '100%';
        
        let position = 0;
        const speed = 1;
        
        function scroll() {
            position -= speed;
            
            // Faire réapparaître le texte juste après sa sortie
            if (position < -(textSpan.offsetWidth)) {
                position = 20; // Petit espace de 20px
            }
            
            textSpan.style.transform = `translateX(${position}px)`;
            currentAnimation = requestAnimationFrame(scroll);
        }
        
        scroll();
    }
    
    // Extraire et mettre à jour le titre depuis le HTML
    function updateTitleFromContent(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const titleElement = tempDiv.querySelector('[data-page-title]');
        
        if (titleElement) {
            const title = titleElement.getAttribute('data-page-title');
            const titleDisplay = document.querySelector('#main-flying-message p');
            
            if (titleDisplay) {
                titleDisplay.textContent = title;
                startScrolling(titleDisplay);
            }
        }
    }
    
    // Écouter l'événement de mise à jour du contenu
    window.addEventListener('pageContentUpdated', function(e) {
        updateTitleFromContent(e.detail.html);
    });
    
    // Écouter l'événement de mise à jour du message défilant
    window.addEventListener('updateScrollingMessage', function(e) {
        const messageElement = document.querySelector('#main-flying-message p');
        if (messageElement && e.detail.message) {
            messageElement.textContent = e.detail.message;
            startScrolling(messageElement.parentElement);
        }
    });
    
    // Démarrer le défilement au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const titleElement = document.querySelector('#main-flying-message p');
        if (titleElement) {
            startScrolling(titleElement);
        }
    });
    
    // Exposer les fonctions si besoin
    window.ScrollingText = {
        start: startScrolling,
        updateFromContent: updateTitleFromContent
    };
    
})();