// public/js/spa.js
/**
 * Navigation SPA pour LVDPA
 * Responsabilité UNIQUE : Gérer la navigation sans rechargement
 */

(function() { // IIFE (Immediately Invoked Function Expression) pour éviter la pollution de l'espace global
    'use strict'; // Active le mode strict JavaScript
    
    // Intercepter les clics sur les liens avec data-spa-link
    document.addEventListener('click', function(e) { // Ajoute un écouteur d'événement click sur tout le document
        const link = e.target.closest('[data-spa-link]'); // Cherche l'élément parent le plus proche avec l'attribut data-spa-link
        if (!link) return; // Si aucun lien trouvé, sort de la fonction
        
        e.preventDefault(); // Empêche le comportement par défaut du lien (navigation normale)
        loadContent(link.href); // Appelle loadContent avec l'URL du lien
    });

    // Charger le contenu
    function loadContent(url) { // Fonction pour charger le contenu via AJAX
        fetch(url, { // Utilise l'API Fetch pour faire une requête
            headers: { // Définit les en-têtes de la requête
                'X-Requested-With': 'XMLHttpRequest' // Indique que c'est une requête AJAX
            }
        })
        .then(response => response.text()) // Convertit la réponse en texte
        .then(html => { // Traite le texte reçu
            try { // Essaie de parser comme JSON
                // Essayer de parser comme JSON (pour login/logout)
                const json = JSON.parse(html); // Tente de convertir le texte en objet JSON
                
                // Si c'est une déconnexion réussie
                if (json.success && json.message && json.message.includes('Déconnexion')) { // Vérifie les conditions de déconnexion
                    // Déclencher un événement pour que session-indicator puisse réagir
                    window.dispatchEvent(new CustomEvent('userLoggedOut')); // Émet un événement personnalisé
                    
                    // Rediriger après un court délai
                    setTimeout(() => { // Attend 300ms
                        window.location.href = json.redirect; // Redirige vers l'URL fournie
                    }, 300);
                    return; // Sort de la fonction
                }
                
                // Si c'est une connexion réussie
                if (json.success && json.userType) { // Vérifie si connexion réussie avec type utilisateur
                    // Déclencher un événement pour mettre à jour le message défilant
                    if (json.welcomeMessage) { // Si un message de bienvenue existe
                        window.dispatchEvent(new CustomEvent('updateScrollingMessage', { // Émet événement avec détails
                            detail: { message: json.welcomeMessage } // Passe le message dans detail
                        }));
                    }
                    
                    // Émettre l'événement pour que session-indicator mette à jour le footer
                    window.dispatchEvent(new CustomEvent('userLoggedIn', { // Émet événement de connexion
                        detail: { userType: json.userType } // Passe le type d'utilisateur
                    }));
                    
                    // Afficher un message de succès (optionnel)
                    if (json.message && !json.welcomeMessage) { // Si message mais pas de welcomeMessage
                        alert(json.message); // Affiche une alerte
                    }
                    
                    // Rediriger si nécessaire
                    if (json.redirect) { // Si une URL de redirection existe
                        setTimeout(() => { // Attend 500ms
                            loadContent(json.redirect); // Charge le contenu de la nouvelle page
                        }, 500);
                    }
                    return; // Sort de la fonction
                }
                
                // Si c'est une erreur
                if (json.error || (json.success === false)) { // Vérifie si erreur ou échec
                    console.error('Erreur:', json.message || json.error); // Log l'erreur dans la console
                    return; // Sort de la fonction
                }
            } catch (e) { // Si le parse JSON échoue
                // Si ce n'est pas du JSON, c'est du HTML normal
                updatePageContent(html); // Appelle updatePageContent avec le HTML
            }
        })
        .catch(error => { // Gère les erreurs de fetch
            console.error('Erreur de chargement:', error); // Log l'erreur
        });
    }

    // Mettre à jour le contenu de la page
    function updatePageContent(html) { // Fonction pour mettre à jour le DOM avec le nouveau HTML
        const contentArea = document.querySelector('#content-area'); // Sélectionne l'élément avec id content-area
        if (contentArea) { // Si l'élément existe
            contentArea.innerHTML = html; // Remplace son contenu HTML
            
            // Déclencher un événement pour informer les autres scripts
            window.dispatchEvent(new CustomEvent('pageContentUpdated', { // Émet un événement personnalisé
                detail: { html: html } // Passe le HTML dans les détails
            }));
            
            // Vérifier si c'est le formulaire de login et l'activer
            setTimeout(function() { // Attend 100ms pour que le DOM soit prêt
                const loginForm = document.getElementById('loginForm'); // Cherche le formulaire de login
                if (loginForm) { // Si le formulaire existe
                    console.log('Formulaire de login détecté, ajout du gestionnaire'); // Log pour debug
                    
                    loginForm.addEventListener('submit', function(e) { // Ajoute écouteur sur submit
                        e.preventDefault(); // Empêche la soumission normale du formulaire
                        console.log('Soumission du formulaire interceptée'); // Log pour debug
                        
                        const formData = new FormData(this); // Crée un objet FormData avec les données du formulaire
                        
                        fetch(this.action, { // Envoie le formulaire via AJAX à l'URL action
                            method: 'POST', // Méthode POST
                            body: formData, // Données du formulaire
                            headers: { // En-têtes
                                'X-Requested-With': 'XMLHttpRequest' // Indique requête AJAX
                            }
                        })
                        .then(response => response.json()) // Parse la réponse en JSON
                        .then(json => { // Traite la réponse JSON
                            console.log('Réponse reçue:', json); // Log pour debug
                            
                            // Si connexion réussie
                            if (json.success && json.userType) { // Vérifie succès avec type utilisateur
                                // Émettre l'événement pour que session-indicator mette à jour le footer
                                window.dispatchEvent(new CustomEvent('userLoggedIn', { // Émet événement connexion
                                    detail: { userType: json.userType } // Avec le type utilisateur
                                }));
                                
                                // Émettre l'événement pour mettre à jour le message défilant
                                if (json.welcomeMessage) { // Si message de bienvenue
                                    window.dispatchEvent(new CustomEvent('updateScrollingMessage', { // Émet événement
                                        detail: { message: json.welcomeMessage } // Avec le message
                                    }));
                                }
                                
                                // Afficher un message temporaire puis recharger la page d'accueil
                                document.querySelector('#content-area').innerHTML = ` 
                                    <div style="text-align: center; padding: 50px;">
                                        <h2 style="color: #37afe5;">Connexion réussie !</h2>
                                        <p>Redirection en cours...</p>
                                    </div>
                                `; // Remplace le contenu par un message de succès
                                
                                // Attendre 1.5 secondes puis charger la page d'accueil
                                setTimeout(() => { // Attend 1500ms
                                    loadContent('/LVDPA/home'); // Charge la page d'accueil
                                }, 1500);
                                
                            } else { // Si échec de connexion
                                // Gérer les erreurs
                                console.error('Erreur de connexion:', json.message || 'Erreur inconnue'); // Log l'erreur
                                if (json.errors) { // Si détails d'erreurs
                                    console.error('Détails:', json.errors); // Log les détails
                                }
                            }
                        })
                        .catch(error => { // Gère les erreurs de fetch
                            console.error('Erreur lors de la connexion:', error); // Log l'erreur
                        });
                    });
                }
            }, 100); // Délai de 100ms
        }
    }
    
    // Exposer loadContent globalement si besoin
    window.SPA = { // Crée un objet global SPA
        loadContent: loadContent // Expose la fonction loadContent
    };
    
})(); // Fin de l'IIFE et exécution immédiate