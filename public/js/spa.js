// public/js/spa.js
/**
 * Navigation SPA pour LVDPA
 * Responsabilité UNIQUE : Gérer la navigation sans rechargement
 */

(function() {
    'use strict';
    
    // Intercepter les clics sur les liens avec data-spa-link
    document.addEventListener('click', function(e) {
        const link = e.target.closest('[data-spa-link]');
        if (!link) return;
        
        e.preventDefault();
        loadContent(link.href);
    });

    // Charger le contenu
    function loadContent(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            try {
                // Essayer de parser comme JSON (pour login/logout)
                const json = JSON.parse(html);
                
                // Si c'est une déconnexion réussie
                if (json.success && json.message && json.message.includes('Déconnexion')) {
                    // Déclencher un événement pour que session-indicator puisse réagir
                    window.dispatchEvent(new CustomEvent('userLoggedOut'));
                    
                    // Rediriger après un court délai
                    setTimeout(() => {
                        window.location.href = json.redirect;
                    }, 300);
                    return;
                }
                
                // Si c'est une connexion réussie
                if (json.success && json.userType) {
                    // Déclencher un événement pour mettre à jour le message défilant
                    if (json.welcomeMessage) {
                        window.dispatchEvent(new CustomEvent('updateScrollingMessage', {
                            detail: { message: json.welcomeMessage }
                        }));
                    }
                    
                    // Émettre l'événement pour que session-indicator mette à jour le footer
                    window.dispatchEvent(new CustomEvent('userLoggedIn', {
                        detail: { userType: json.userType }
                    }));
                    
                    // Afficher un message de succès (optionnel)
                    if (json.message && !json.welcomeMessage) {
                        alert(json.message);
                    }
                    
                    // Rediriger si nécessaire
                    if (json.redirect) {
                        setTimeout(() => {
                            loadContent(json.redirect);
                        }, 500);
                    }
                    return;
                }
                
                // Si c'est une erreur
                if (json.error || (json.success === false)) {
                    console.error('Erreur:', json.message || json.error);
                    return;
                }
            } catch (e) {
                // Si ce n'est pas du JSON, c'est du HTML normal
                updatePageContent(html);
            }
        })
        .catch(error => {
            console.error('Erreur de chargement:', error);
        });
    }

    // Mettre à jour le contenu de la page
    function updatePageContent(html) {
        const contentArea = document.querySelector('#content-area');
        if (contentArea) {
            contentArea.innerHTML = html;
            
            // Déclencher un événement pour informer les autres scripts
            window.dispatchEvent(new CustomEvent('pageContentUpdated', {
                detail: { html: html }
            }));
            
            // Vérifier si c'est le formulaire de login et l'activer
            setTimeout(function() {
                const loginForm = document.getElementById('loginForm');
                if (loginForm) {
                    console.log('Formulaire de login détecté, ajout du gestionnaire');
                    
                    loginForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Soumission du formulaire interceptée');
                        
                        const formData = new FormData(this);
                        
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(json => {
                            console.log('Réponse reçue:', json);
                            
                            // Si connexion réussie
                            if (json.success && json.userType) {
                                // Émettre l'événement pour que session-indicator mette à jour le footer
                                window.dispatchEvent(new CustomEvent('userLoggedIn', {
                                    detail: { userType: json.userType }
                                }));
                                
                                // Émettre l'événement pour mettre à jour le message défilant
                                if (json.welcomeMessage) {
                                    window.dispatchEvent(new CustomEvent('updateScrollingMessage', {
                                        detail: { message: json.welcomeMessage }
                                    }));
                                }
                                
                                // Afficher un message temporaire puis recharger la page d'accueil
                                document.querySelector('#content-area').innerHTML = `
                                    <div style="text-align: center; padding: 50px;">
                                        <h2 style="color: #37afe5;">Connexion réussie !</h2>
                                        <p>Redirection en cours...</p>
                                    </div>
                                `;
                                
                                // Attendre 1.5 secondes puis charger la page d'accueil
                                setTimeout(() => {
                                    loadContent('/LVDPA/home');
                                }, 1500);
                                
                            } else {
                                // Gérer les erreurs
                                console.error('Erreur de connexion:', json.message || 'Erreur inconnue');
                                if (json.errors) {
                                    console.error('Détails:', json.errors);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la connexion:', error);
                        });
                    });
                }
            }, 100);
        }
    }
    
    // Exposer loadContent globalement si besoin
    window.SPA = {
        loadContent: loadContent
    };
    
})();