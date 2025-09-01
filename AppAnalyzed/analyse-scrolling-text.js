// public/js/scrolling-text.js
/**
 * Gestion du défilement du titre
 * Responsabilité UNIQUE : Faire défiler le texte dans #main-flying-message
 */

(function() { // IIFE pour encapsuler le code
    'use strict'; // Active le mode strict
    
    let currentAnimation = null; // Variable pour stocker l'ID de l'animation en cours
    
    // Fonction pour faire défiler le texte
    function startScrolling(element) { // Fonction qui prend un élément DOM en paramètre
        // Arrêter l'animation précédente si elle existe
        if (currentAnimation) { // Si une animation est en cours
            cancelAnimationFrame(currentAnimation); // L'annuler
        }
        
        const originalText = element.textContent; // Récupère le texte original de l'élément
        
        // Créer un conteneur pour le défilement
        element.innerHTML = `<span>${originalText}</span>`; // Entoure le texte dans un span
        const textSpan = element.querySelector('span'); // Sélectionne le span créé
        
        // Styles nécessaires
        element.style.overflow = 'hidden'; // Cache le débordement du texte
        element.style.whiteSpace = 'nowrap'; // Empêche le retour à la ligne
        textSpan.style.display = 'inline-block'; // Affichage en ligne-bloc pour le span
        textSpan.style.paddingLeft = '100%'; // Décale le texte à droite (hors de la vue)
        
        let position = 0; // Position initiale du texte
        const speed = 1; // Vitesse de défilement en pixels par frame
        
        function scroll() { // Fonction récursive pour l'animation
            position -= speed; // Déplace le texte vers la gauche
            
            // Faire réapparaître le texte juste après sa sortie
            if (position < -(textSpan.offsetWidth)) { // Si le texte est complètement sorti à gauche
                position = 20; // Le repositionner à droite avec un espace de 20px
            }
            
            textSpan.style.transform = `translateX(${position}px)`; // Applique la transformation CSS
            currentAnimation = requestAnimationFrame(scroll); // Demande la prochaine frame d'animation
        }
        
        scroll(); // Démarre l'animation
    }
    
    // Extraire et mettre à jour le titre depuis le HTML
    function updateTitleFromContent(html) { // Fonction qui prend du HTML en paramètre
        const tempDiv = document.createElement('div'); // Crée un div temporaire
        tempDiv.innerHTML = html; // Insère le HTML dans le div
        const titleElement = tempDiv.querySelector('[data-page-title]'); // Cherche l'élément avec data-page-title
        
        if (titleElement) { // Si l'élément existe
            const title = titleElement.getAttribute('data-page-title'); // Récupère la valeur de l'attribut
            const titleDisplay = document.querySelector('#main-flying-message p'); // Sélectionne le paragraphe du message défilant
            
            if (titleDisplay) { // Si l'élément d'affichage existe
                titleDisplay.textContent = title; // Met à jour le texte
                startScrolling(titleDisplay); // Redémarre le défilement avec le nouveau texte
            }
        }
    }
    
    // Écouter l'événement de mise à jour du contenu
    window.addEventListener('pageContentUpdated', function(e) { // Écoute l'événement personnalisé
        updateTitleFromContent(e.detail.html); // Appelle updateTitleFromContent avec le HTML reçu
    });
    
    // Écouter l'événement de mise à jour du message défilant
    window.addEventListener('updateScrollingMessage', function(e) { // Écoute l'événement personnalisé
        const messageElement = document.querySelector('#main-flying-message p'); // Sélectionne le paragraphe
        if (messageElement && e.detail.message) { // Si l'élément existe et qu'il y a un message
            messageElement.textContent = e.detail.message; // Met à jour le texte
            startScrolling(messageElement.parentElement); // Démarre le défilement sur l'élément parent
        }
    });
    
    // Démarrer le défilement au chargement de la page
    document.addEventListener('DOMContentLoaded', function() { // Attend que le DOM soit chargé
        const titleElement = document.querySelector('#main-flying-message p'); // Sélectionne le paragraphe
        if (titleElement) { // Si l'élément existe
            startScrolling(titleElement); // Démarre le défilement initial
        }
    });
    
    // Exposer les fonctions si besoin
    window.ScrollingText = { // Crée un objet global ScrollingText
        start: startScrolling, // Expose la fonction startScrolling
        updateFromContent: updateTitleFromContent // Expose la fonction updateTitleFromContent
    };
    
})(); // Fin de l'IIFE et exécution du script