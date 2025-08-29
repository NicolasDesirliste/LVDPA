<?php
// app/config/sidebar-config.php
namespace App\Config; // Déclaration du namespace (non nécessaire ici car le fichier retourne juste un tableau)

/**
 * Configuration de la sidebar
 * 
 * Structure :
 * - title : Titre du menu
 * - tooltip_header : Titre dans la tooltip
 * - visibility : Qui peut voir ce menu
 *   - 'all' : Tout le monde
 *   - 'guest' : Visiteurs uniquement
 *   - 'authenticated' : Connectés uniquement
 *   - 'role:admin' : Admin uniquement
 *   - 'role:moderateur' : Modérateur uniquement
 *   - 'type:professionnel' : Professionnels uniquement
 * - links : Liste des liens du menu
 *   - text : Texte du lien
 *   - href : URL du lien
 *   - spa : true/false pour data-spa-link
 *   - visibility : Même logique que pour les menus
 */
// ======================================================================================
// spoiler alert: Les => '#' sont des pages en cours de considération à l'heure actuelle!
// ======================================================================================

return [ // Le fichier retourne directement un tableau associatif
    'menus' => [ // Clé principale contenant tous les menus
        [ // Premier menu : Espace Administration
            'title' => 'Espace Administration', // Titre affiché du menu
            'tooltip_header' => 'Administration du site', // Texte de l'infobulle
            'visibility' => 'role:admin', // Visible uniquement pour les admins
            'links' => [ // tableau des liens du menu
                ['text' => 'Au rapport!', 'href' => '#'], // Lien simple avec texte et URL
                ['text' => 'Signalements', 'href' => '#'], // Pas de visibility = hérite de 'all'
                ['text' => 'Utilisateurs', 'href' => '#'],
                ['text' => 'Archives', 'href' => '#'],
                ['text' => 'Message global', 'href' => '#'],
                ['text' => 'Mail global', 'href' => '#']
            ]
        ],
        
        [ // Deuxième menu : Espace Modérateurs
            'title' => 'Espace Modérateurs', // Titre du menu
            'tooltip_header' => 'Outils de modération', // Texte de l'infobulle
            'visibility' => 'role:moderateur', // Visible uniquement pour les modérateurs
            'links' => [ // Liens du menu modérateur
                ['text' => 'Signalements en attente', 'href' => '#'],
                ['text' => 'Utilisateurs signalés', 'href' => '#'],
                ['text' => 'Posts à modérer', 'href' => '#'],
                ['text' => 'Messages signalés', 'href' => '#'],
                ['text' => 'Historique modération', 'href' => '#'],
                ['text' => 'Guide modération', 'href' => '#']
            ] 
        ],
        
        [ // Troisième menu : Menu principal
            'title' => 'Bienvenue sur le site!', // Titre du menu
            'tooltip_header' => 'Menu de navigation', // Texte de l'infobulle
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens avec visibilité conditionnelle
                ['text' => 'La mission LVDPA', 'href' => '#'], // Lien visible pour tous
                [
                    'text' => 'Déconnexion', // Texte du lien
                    'href' => '/LVDPA/logout', // URL de déconnexion
                    'visibility' => 'authenticated' // Visible seulement si connecté
                ],
                [
                    'text' => 'Connexion', // Texte du lien
                    'href' => '/LVDPA/login', // URL de connexion
                    'spa' => true, // Indique que c'est un lien SPA
                    'visibility' => 'guest' // Visible seulement si non connecté
                ],
                [
                    'text' => 'Inscription', // Texte du lien
                    'href' => '/LVDPA/register', // URL d'inscription
                    'spa' => true, // Lien SPA
                    'visibility' => 'guest' // Visible seulement si non connecté
                ]
            ]
        ],
        
        [ // Quatrième menu : Espace Personnel
            'title' => 'Espace Personnel', // Titre du menu
            'tooltip_header' => 'Mon tableau de bord', // Texte de l'infobulle
            'visibility' => 'authenticated', // Visible seulement si connecté
            'links' => [ // Liens de l'espace personnel
                ['text' => 'Mon compte', 'href' => '#'], // Lien standard
                [
                    'text' => 'Mon profil', // Texte du lien
                    'href' => '#', // URL par défaut
                    // Note : La logique de redirection différente pour les professionnels
                    // sera gérée dans le contrôleur ou le service
                ], 
                ['text' => 'Mes messages', 'href' => '#'],
                ['text' => 'Mes posts', 'href' => '#'],
                ['text' => 'Mes paramètres', 'href' => '#'],
                ['text' => 'Mes formulaires', 'href' => '#'],
                [ 
                    'text' => 'Consultations', // Texte du lien
                    'href' => '#', // URL
                    'visibility' => 'type:professionnel' // Visible seulement pour professionnels
                ],
                [
                    'text' => 'Demandes', // Texte du lien
                    'href' => '#', // URL
                    'visibility' => 'type:professionnel' // Visible seulement pour professionnels
                ]
            ]
        ],
        
        [ // Cinquième menu : Professionnels Inscrits
            'title' => 'Professionnels Inscrits', // Titre du menu
            'tooltip_header' => 'Menu de navigation', // Texte de l'infobulle
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens du menu
                [
                    'text' => 'Rechercher un Professionnel', // Texte du lien
                    'href' => '/LVDPA/search', // URL de recherche
                    'id' => 'searchProBtn' // ID HTML pour JavaScript
                ],
                [
                    'text' => 'Formulaire de Contact', // Texte du lien
                    'href' => '/LVDPA/contactpro', // URL du formulaire
                    'id' => 'contactBtn' // ID HTML pour JavaScript
                ]
            ]
        ],
        
        [ // Sixième menu : Modération du Site
            'title' => 'Modération du Site', // Titre du menu
            'tooltip_header' => 'Des questions, suggestions ou commentaires à nous communiquer?', // Texte long pour l'infobulle
            'visibility' => 'authenticated', // Visible seulement si connecté
            'links' => [ // Un seul lien dans ce menu
                ['text' => 'Contacter un Modérateur', 'href' => '#']
            ]
        ],
        
        [ // Septième menu : Forum de discussion
            'title' => 'Forum de discussion', // Titre du menu
            'tooltip_header' => 'Différents Forums', // Texte de l'infobulle
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens vers différentes sections du forum
                ['text' => 'Derniers Posts', 'href' => '/LVDPA/index.php?page=forumpage'], // URL avec paramètre GET
                ['text' => 'Articles des Avocats', 'href' => '#'],
                ['text' => 'Articles des Psychologues', 'href' => '#'],
                ['text' => 'Articles des Médiateurs', 'href' => '#']
            ]
        ],
        
        [ // Huitième menu : Informations Cruciales
            'title' => 'Informations Cruciales', // Titre du menu
            'tooltip_header' => 'Les Choses qu\'on ne vous dit pas', // Texte avec apostrophe échappée
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens informatifs
                ['text' => 'Procédures Légales', 'href' => '#'],
                ['text' => 'Documents Importants', 'href' => '#'],
                ['text' => 'Délais Légaux', 'href' => '#'],
                ['text' => 'Droits & Devoirs des Avocats', 'href' => '#'],
                ['text' => 'Droits et Devoirs des Juges', 'href' => '#'],
                ['text' => 'Bien préparer son audience', 'href' => '#'],
                ['text' => 'Les pièces admissibles', 'href' => '#']
            ]
        ],
        
        [ // Neuvième menu : Vos Droits
            'title' => 'Vos Droits', // Titre du menu
            'tooltip_header' => 'Ce qu\'il faut savoir', // Texte avec apostrophe échappée
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens sur les droits
                ['text' => 'Droits parentaux', 'href' => '#'],
                ['text' => 'Garde partagée', 'href' => '#'],
                ['text' => 'Pension alimentaire', 'href' => '#'],
                ['text' => 'Droits de visite', 'href' => '#'],
                ['text' => 'Recours légaux', 'href' => '#']
            ]
        ],
        
        [ // Dixième menu : Conflits
            'title' => 'Conflits', // Titre du menu
            'tooltip_header' => 'Définitions Légales', // Texte de l'infobulle
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens sur types de conflits
                ['text' => 'Violences Physiques', 'href' => '#'],
                ['text' => 'Violences Verbales', 'href' => '#'],
                ['text' => 'Violences Psychologiques', 'href' => '#'],
                ['text' => 'Violences Réactives', 'href' => '#'],
                ['text' => 'Aliénation Parentale', 'href' => '#']
            ]
        ],
        
        [ // Onzième menu : Pathologies Psychologiques
            'title' => 'Pathologies Psychologiques', // Titre du menu
            'tooltip_header' => 'Pathologies Différentes', // Texte de l'infobulle
            'visibility' => 'all', // Visible pour tous
            'links' => [ // Liens sur pathologies
                ['text' => 'Dépression post-séparation', 'href' => '#'],
                ['text' => 'Immaturité Emotionnelle', 'href' => '#'],
                ['text' => 'Perversion Narcissique', 'href' => '#'],
                ['text' => 'Narcissisme Malin', 'href' => '#'],
                ['text' => 'Troubles de la Personnalité', 'href' => '#'],
                ['text' => 'Personnalité Borderline', 'href' => '#']
            ]
        ]
    ] // Fin du tableau des menus
]; // Fin du 'return'