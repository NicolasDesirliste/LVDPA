// public/js/authjs/login-form.js

// Fonction pour attacher le gestionnaire au formulaire
function attachLoginFormHandler() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm && !loginForm.hasAttribute('data-handler-attached')) {
        console.log('Attachement du gestionnaire au formulaire de connexion');
        loginForm.setAttribute('data-handler-attached', 'true');
        
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
                // Traiter directement ici pour le moment
                if (json.success && json.userType) {
                    window.dispatchEvent(new CustomEvent('userLoggedIn', {
                        detail: { userType: json.userType }
                    }));
                    
                    if (json.welcomeMessage) {
                        window.dispatchEvent(new CustomEvent('updateScrollingMessage', {
                            detail: { message: json.welcomeMessage }
                        }));
                    }
                }
            })
            .catch(error => {
                console.error('Erreur de connexion:', error);
            });
        });
    }
}

// Écouter quand le contenu est mis à jour par le SPA
window.addEventListener('pageContentUpdated', attachLoginFormHandler);

// Essayer aussi au chargement initial
setTimeout(attachLoginFormHandler, 100);