<?php
// app/config/sidebar-config.php
namespace App\Config;

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

return [
    'menus' => [
        [
            'title' => 'Espace Administration',
            'tooltip_header' => 'Administration du site',
            'visibility' => 'role:admin',
            'links' => [
                ['text' => 'Au rapport!', 'href' => '#'],
                ['text' => 'Signalements', 'href' => '#'],
                ['text' => 'Utilisateurs', 'href' => '#'],
                ['text' => 'Archives', 'href' => '#'],
                ['text' => 'Message global', 'href' => '#'],
                ['text' => 'Mail global', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Espace Modérateurs',
            'tooltip_header' => 'Outils de modération',
            'visibility' => 'role:moderateur',
            'links' => [
                ['text' => 'Signalements en attente', 'href' => '#'],
                ['text' => 'Utilisateurs signalés', 'href' => '#'],
                ['text' => 'Posts à modérer', 'href' => '#'],
                ['text' => 'Messages signalés', 'href' => '#'],
                ['text' => 'Historique modération', 'href' => '#'],
                ['text' => 'Guide modération', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Bienvenue sur le site!',
            'tooltip_header' => 'Menu de navigation',
            'visibility' => 'all',
            'links' => [
                ['text' => 'La mission LVDPA', 'href' => '#'],
                [
                    'text' => 'Déconnexion', 
                    'href' => '/LVDPA/logout',
                    'visibility' => 'authenticated'
                ],
                [
                    'text' => 'Connexion', 
                    'href' => '/LVDPA/login',
                    'spa' => true,
                    'visibility' => 'guest'
                ],
                [
                    'text' => 'Inscription', 
                    'href' => '/LVDPA/register',
                    'spa' => true,
                    'visibility' => 'guest'
                ]
            ]
        ],
        
        [
            'title' => 'Espace Personnel',
            'tooltip_header' => 'Mon tableau de bord',
            'visibility' => 'authenticated',
            'links' => [
                ['text' => 'Mon compte', 'href' => '#'],
                [
                    'text' => 'Mon profil',
                    'href' => '#',
                    // Note : La logique de redirection différente pour les professionnels
                ],
                ['text' => 'Mes messages', 'href' => '#'],
                ['text' => 'Mes posts', 'href' => '#'],
                ['text' => 'Mes paramètres', 'href' => '#'],
                ['text' => 'Mes formulaires', 'href' => '#'],
                [
                    'text' => 'Consultations',
                    'href' => '#',
                    'visibility' => 'type:professionnel'
                ],
                [
                    'text' => 'Demandes',
                    'href' => '#',
                    'visibility' => 'type:professionnel'
                ]
            ]
        ],
        
        [
            'title' => 'Professionnels Inscrits',
            'tooltip_header' => 'Menu de navigation',
            'visibility' => 'all',
            'links' => [
                [
                    'text' => 'Rechercher un Professionnel',
                    'href' => '/LVDPA/search',
                    'id' => 'searchProBtn'
                ],
                [
                    'text' => 'Formulaire de Contact',
                    'href' => '/LVDPA/contactpro', 
                    'id' => 'contactBtn'
                ]
            ]
        ],
        
        [
            'title' => 'Modération du Site',
            'tooltip_header' => 'Des questions, suggestions ou commentaires à nous communiquer?',
            'visibility' => 'authenticated',
            'links' => [
                ['text' => 'Contacter un Modérateur', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Forum de discussion',
            'tooltip_header' => 'Différents Forums',
            'visibility' => 'all',
            'links' => [
                ['text' => 'Derniers Posts', 'href' => '/LVDPA/index.php?page=forumpage'],
                ['text' => 'Articles des Avocats', 'href' => '#'],
                ['text' => 'Articles des Psychologues', 'href' => '#'],
                ['text' => 'Articles des Médiateurs', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Informations Cruciales',
            'tooltip_header' => 'Les Choses qu\'on ne vous dit pas',
            'visibility' => 'all',
            'links' => [
                ['text' => 'Procédures Légales', 'href' => '#'],
                ['text' => 'Documents Importants', 'href' => '#'],
                ['text' => 'Délais Légaux', 'href' => '#'],
                ['text' => 'Droits & Devoirs des Avocats', 'href' => '#'],
                ['text' => 'Droits et Devoirs des Juges', 'href' => '#'],
                ['text' => 'Bien préparer son audience', 'href' => '#'],
                ['text' => 'Les pièces admissibles', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Vos Droits',
            'tooltip_header' => 'Ce qu\'il faut savoir',
            'visibility' => 'all',
            'links' => [
                ['text' => 'Droits parentaux', 'href' => '#'],
                ['text' => 'Garde partagée', 'href' => '#'],
                ['text' => 'Pension alimentaire', 'href' => '#'],
                ['text' => 'Droits de visite', 'href' => '#'],
                ['text' => 'Recours légaux', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Conflits',
            'tooltip_header' => 'Définitions Légales',
            'visibility' => 'all',
            'links' => [
                ['text' => 'Violences Physiques', 'href' => '#'],
                ['text' => 'Violences Verbales', 'href' => '#'],
                ['text' => 'Violences Psychologiques', 'href' => '#'],
                ['text' => 'Violences Réactives', 'href' => '#'],
                ['text' => 'Aliénation Parentale', 'href' => '#']
            ]
        ],
        
        [
            'title' => 'Pathologies Psychologiques',
            'tooltip_header' => 'Pathologies Différentes',
            'visibility' => 'all',
            'links' => [
                ['text' => 'Dépression post-séparation', 'href' => '#'],
                ['text' => 'Immaturité Emotionnelle', 'href' => '#'],
                ['text' => 'Perversion Narcissique', 'href' => '#'],
                ['text' => 'Narcissisme Malin', 'href' => '#'],
                ['text' => 'Troubles de la Personnalité', 'href' => '#'],
                ['text' => 'Personnalité Borderline', 'href' => '#']
            ]
        ]
    ]
];