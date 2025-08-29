
### Lignes 1-5 : En-tête du fichier
```php
<?php
// app/services/SidebarService.php
namespace App\Services;

use App\Services\SessionManager;
```
- **Ligne 1** : Ouverture du tag PHP
- **Ligne 2** : Commentaire indiquant l'emplacement physique du fichier
- **Ligne 3** : Déclaration du namespace `App\Services` pour l'autoloading PSR-4
- **Ligne 5** : Import de la classe SessionManager du même namespace

### Lignes 7-13 : Documentation PHPDoc
```php
/**
 * Classe SidebarService
 * 
 * Responsabilité UNIQUE : Générer la structure de la sidebar selon les permissions
 * - Lit la configuration
 * - Applique les règles de visibilité
 * - Retourne uniquement les éléments autorisés
 */
```
- Bloc de commentaire PHPDoc standard
- Décrit le principe de responsabilité unique (SOLID)
- Liste les 3 actions principales de la classe

### Lignes 14-15 : Déclaration de classe et propriété
```php
class SidebarService {
    private array $config;
```
- **Ligne 14** : Déclaration de la classe SidebarService
- **Ligne 15** : Propriété privée `$config` typée comme array (PHP 7.4+)

### Lignes 17-23 : Constructeur
```php
/**
 * Constructeur
 */
public function __construct() {
    // Charger la configuration
    $this->config = require dirname(__DIR__) . '/config/sidebar-config.php';
}
```
- **Ligne 20** : Méthode constructeur publique
- **Ligne 22** : Charge le fichier de configuration
  - `dirname(__DIR__)` : Obtient le répertoire parent du répertoire parent
  - Concatène avec `/config/sidebar-config.php`
  - `require` exécute le fichier qui retourne un array
  - Assigne le résultat à `$this->config`

### Lignes 25-46 : Méthode getFilteredMenus()
```php
/**
 * Récupère les menus filtrés selon les permissions de l'utilisateur
 * 
 * @return array
 */
public function getFilteredMenus(): array {
    $filteredMenus = [];
    
    foreach ($this->config['menus'] as $menu) {
        // Vérifier si le menu est visible pour l'utilisateur actuel
        if ($this->isVisible($menu['visibility'])) {
            // Filtrer les liens du menu
            $filteredLinks = $this->filterLinks($menu['links']);
            
            // N'ajouter le menu que s'il reste des liens visibles
            if (!empty($filteredLinks)) {
                $menu['links'] = $filteredLinks;
                $filteredMenus[] = $menu;
            }
        }
    }
    
    return $filteredMenus;
}
```
- **Ligne 30** : Signature avec type de retour array
- **Ligne 31** : Initialise un array vide pour stocker les résultats
- **Ligne 33** : Itère sur chaque menu dans `$this->config['menus']`
- **Ligne 35** : Vérifie la visibilité du menu avec la méthode `isVisible()`
- **Ligne 37** : Si visible, filtre les liens du menu
- **Ligne 40** : Vérifie que le tableau de liens filtrés n'est pas vide
- **Ligne 41** : Remplace les liens originaux par les liens filtrés
- **Ligne 42** : Ajoute le menu modifié au tableau des menus filtrés
- **Ligne 46** : Retourne le tableau final

### Lignes 48-67 : Méthode filterLinks()
```php
/**
 * Filtre les liens selon les permissions
 * 
 * @param array $links
 * @return array
 */
private function filterLinks(array $links): array {
    $filteredLinks = [];
    
    foreach ($links as $link) {
        // Si pas de règle de visibilité sur le lien, il est visible
        $linkVisibility = $link['visibility'] ?? 'all';
        
        if ($this->isVisible($linkVisibility)) {
            // Adapter l'URL pour les professionnels si nécessaire
            $link = $this->adaptLinkForUser($link);
            $filteredLinks[] = $link;
        }
    }
    
    return $filteredLinks;
}
```
- **Ligne 53** : Méthode privée avec paramètre typé array
- **Ligne 54** : Initialise array vide pour résultats
- **Ligne 56** : Itère sur chaque lien
- **Ligne 58** : Utilise l'opérateur null coalescing `??`
  - Si `$link['visibility']` existe, utilise sa valeur
  - Sinon, utilise 'all' comme valeur par défaut
- **Ligne 60** : Vérifie la visibilité du lien
- **Ligne 62** : Adapte le lien selon l'utilisateur
- **Ligne 63** : Ajoute le lien au tableau filtré
- **Ligne 67** : Retourne les liens filtrés

### Lignes 69-98 : Méthode isVisible()
```php
/**
 * Vérifie si un élément est visible selon la règle de visibilité
 * 
 * @param string $visibility
 * @return bool
 */
private function isVisible(string $visibility): bool {
    switch ($visibility) {
        case 'all':
            return true;
            
        case 'guest':
            return !SessionManager::isLoggedIn();
            
        case 'authenticated':
            return SessionManager::isLoggedIn();
            
        case 'role:admin':
            return SessionManager::isAdmin();
            
        case 'role:moderateur':
            return SessionManager::isModerator();
            
        case 'type:professionnel':
            return SessionManager::isProfessional();
            
        default:
            // Par défaut, on cache l'élément si la règle n'est pas reconnue
            return false;
    }
}
```
- **Ligne 76** : Méthode privée avec paramètre string, retour booléen
- **Ligne 77** : Structure switch sur la visibilité
- **Ligne 78-79** : 'all' retourne toujours true
- **Ligne 81-82** : 'guest' retourne true si NON connecté (opérateur `!`)
- **Ligne 84-85** : 'authenticated' retourne true si connecté
- **Ligne 87-88** : 'role:admin' délègue à SessionManager::isAdmin()
- **Ligne 90-91** : 'role:moderateur' délègue à SessionManager::isModerator()
- **Ligne 93-94** : 'type:professionnel' délègue à SessionManager::isProfessional()
- **Ligne 96-98** : Cas par défaut retourne false (sécurité)

### Lignes 100-113 : Méthode adaptLinkForUser()
```php
/**
 * Adapte certains liens selon le type d'utilisateur
 * 
 * @param array $link
 * @return array
 */
private function adaptLinkForUser(array $link): array {
    // Exemple : adapter l'URL de "Mon profil" pour les professionnels
    if ($link['text'] === 'Mon profil' && SessionManager::isProfessional()) {
        $link['href'] = '/LVDPA/profil-professionnel';
        // Optionnel : ajouter une classe CSS pour styling différent
        $link['class'] = 'professionnel-link';
    }
    
    return $link;
}
```
- **Ligne 107** : Méthode privée, prend et retourne un array
- **Ligne 109** : Condition avec ET logique (`&&`)
  - Vérifie que le texte est exactement "Mon profil"
  - ET que l'utilisateur est un professionnel
- **Ligne 110** : Modifie l'URL du lien
- **Ligne 112** : Ajoute une classe CSS au lien
- **Ligne 115** : Retourne le lien (modifié ou non)

### Lignes 115-130 : Méthode getMenuByTitle()
```php
/**
 * Récupère un menu spécifique par son titre
 * 
 * @param string $title
 * @return array|null
 */
public function getMenuByTitle(string $title): ?array {
    $allMenus = $this->getFilteredMenus();
    
    foreach ($allMenus as $menu) {
        if ($menu['title'] === $title) {
            return $menu;
        }
    }
    
    return null;
}
```
- **Ligne 122** : Type de retour nullable `?array` (array ou null)
- **Ligne 123** : Récupère tous les menus filtrés
- **Ligne 125** : Parcourt les menus
- **Ligne 126** : Compare le titre avec égalité stricte `===`
- **Ligne 127** : Retourne immédiatement si trouvé
- **Ligne 131** : Retourne null si non trouvé

### Lignes 132-140 : Méthode hasAdministrativeAccess()
```php
/**
 * Vérifie si l'utilisateur a accès à au moins un menu admin/modérateur
 * 
 * @return bool
 */
public function hasAdministrativeAccess(): bool {
    return SessionManager::isAdmin() || SessionManager::isModerator();
}
```
- **Ligne 138** : Méthode publique retournant un booléen
- **Ligne 139** : Utilise l'opérateur OR logique `||`
  - Retourne true si admin OU modérateur

### Lignes 142-150 : Méthode countVisibleMenus()
```php
/**
 * Compte le nombre de menus visibles pour l'utilisateur
 * 
 * @return int
 */
public function countVisibleMenus(): int {
    return count($this->getFilteredMenus());
}
```
- **Ligne 148** : Retourne un entier
- **Ligne 149** : Utilise la fonction `count()` sur le résultat de `getFilteredMenus()`

### Lignes 152-172 : Méthode getLinksByVisibility()
```php
/**
 * Récupère tous les liens d'un certain type
 * Utile pour générer des sitemaps ou des menus secondaires
 * 
 * @param string $visibility
 * @return array
 */
public function getLinksByVisibility(string $visibility): array {
    $links = [];
    
    foreach ($this->config['menus'] as $menu) {
        if ($this->isVisible($menu['visibility'])) {
            foreach ($menu['links'] as $link) {
                $linkVisibility = $link['visibility'] ?? 'all';
                if ($linkVisibility === $visibility) {
                    $links[] = $link;
                }
            }
        }
    }
    
    return $links;
}
```
- **Ligne 160** : Méthode publique avec paramètre visibility
- **Ligne 161** : Initialise array vide
- **Ligne 163** : Première boucle sur les menus
- **Ligne 164** : Vérifie visibilité du menu
- **Ligne 165** : Deuxième boucle sur les liens du menu
- **Ligne 166** : Récupère visibilité du lien avec défaut 'all'
- **Ligne 167** : Compare avec la visibilité recherchée (égalité stricte)
- **Ligne 168** : Ajoute le lien si correspond
- **Ligne 173** : Retourne tous les liens trouvés

## 2. sidebar-config.php - Structure exacte

### Lignes 1-3 : En-tête
```php
<?php
// app/config/sidebar-config.php
namespace App\Config;
```
- Namespace déclaré mais non utilisé (le fichier retourne juste un array)

### Ligne 25 : Retour de la configuration
```php
return [
    'menus' => [
```
- Le fichier retourne directement un array associatif
- Clé principale 'menus' contenant un tableau de menus

### Structure d'un menu (exemple lignes 27-38)
```php
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
]
```
- Chaque menu a 4 clés : title, tooltip_header, visibility, links
- Les liens sont des arrays avec au minimum 'text' et 'href'

### Liens avec visibilité (exemple lignes 54-66)
```php
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
]
```
- Certains liens ont une clé 'visibility' optionnelle
- La clé 'spa' apparaît sur certains liens (valeur booléenne)

## 3. ApiController.php - Code exact

### Lignes 13-23 : Classe et constructeur
```php
class ApiController extends Controller {
    private SidebarService $sidebarService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct();
        $this->sidebarService = new SidebarService();
    }
```
- **Ligne 13** : Hérite de la classe Controller
- **Ligne 14** : Propriété privée typée SidebarService
- **Ligne 20** : Appelle le constructeur parent
- **Ligne 21** : Instancie un nouveau SidebarService

### Lignes 29-40 : Méthode getSidebarMenus()
```php
public function getSidebarMenus(): void {
    // Récupérer les menus filtrés selon les permissions
    $menus = $this->sidebarService->getFilteredMenus();
    
    // Retourner en JSON
    $this->json([
        'success' => true,
        'menus' => $menus,
        'userType' => $this->getCurrentUserType(),
        'isAuthenticated' => $this->isAuthenticated()
    ]);
}
```
- **Ligne 29** : Type de retour void
- **Ligne 31** : Appelle getFilteredMenus() du service
- **Ligne 34-39** : Appelle la méthode json() héritée avec un array
  - 'success' => true
  - 'menus' => résultat du service
  - 'userType' => appel à méthode héritée
  - 'isAuthenticated' => appel à méthode héritée

### Lignes 46-55 : Méthode getSessionStatus()
```php
public function getSessionStatus(): void {
    $this->json([
        'isAuthenticated' => $this->isAuthenticated(),
        'userType' => $this->getCurrentUserType(),
        'userId' => $this->getCurrentUserId(),
        'isAdmin' => \App\Services\SessionManager::isAdmin(),
        'isModerator' => \App\Services\SessionManager::isModerator(),
        'isProfessional' => \App\Services\SessionManager::isProfessional()
    ]);
}
```
- **Ligne 47-53** : Appel direct à json() avec array
- **Ligne 50-52** : Utilise le namespace complet pour SessionManager

## 4. SessionManager.php - Méthodes exactes

### Lignes 7-29 : initSession()
```php
public static function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        
        session_start();
        
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}
```
- **Ligne 8** : Vérifie si session non active avec PHP_SESSION_NONE
- **Ligne 11** : Configure cookie httponly à 1
- **Ligne 16** : Configure mode strict à 1
- **Ligne 18** : Démarre la session
- **Ligne 21** : Vérifie si 'initiated' existe dans $_SESSION
- **Ligne 22** : Régénère l'ID avec true (supprime l'ancienne)
- **Ligne 23** : Marque comme initiée

### Lignes 36-49 : setUser()
```php
public static function setUser($userData) {
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['user_pseudo'] = $userData['pseudo'];
    $_SESSION['user_role'] = $userData['role'];
    $_SESSION['user_type_utilisateur'] = $userData['type_utilisateur'];
    $_SESSION['last_activity'] = time();
    
    $_SESSION['logged_in'] = true;
}
```
- **Ligne 38** : Régénère l'ID de session
- **Lignes 40-44** : Stocke les données dans $_SESSION
- **Ligne 44** : Utilise time() pour le timestamp
- **Ligne 47** : Met logged_in à true

### Lignes 54-59 : isLoggedIn()
```php
public static function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           !empty($_SESSION['user_id']) && 
           isset($_SESSION['logged_in']) && 
           $_SESSION['logged_in'] === true;
}
```
- Retourne le résultat de 4 conditions avec AND logique
- Vérifie existence, non-vide, et valeur true

### Lignes 96-114 : Méthodes de vérification de rôle
```php
public static function isAdmin() {
    return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

public static function isModerator() {
    return self::isLoggedIn() && $_SESSION['user_role'] === 'moderateur';
}

public static function isProfessional() {
    $professionalTypes = ['avocat', 'psychologue', 'mediateur'];
    return self::isLoggedIn() && in_array($_SESSION['user_type_utilisateur'] ?? '', $professionalTypes);
}
```
- Chaque méthode vérifie d'abord isLoggedIn()
- isProfessional() utilise in_array() avec un tableau de types

## Flux exact d'exécution

1. Requête arrive sur ApiController::getSidebarMenus()
2. ApiController instancie SidebarService dans son constructeur
3. SidebarService charge sidebar-config.php dans son constructeur
4. getSidebarMenus() appelle getFilteredMenus()
5. getFilteredMenus() boucle sur chaque menu
6. Pour chaque menu, vérifie isVisible() qui appelle SessionManager
7. Si visible, appelle filterLinks() sur les liens du menu
8. filterLinks() vérifie chaque lien et appelle adaptLinkForUser()
9. Retourne les menus filtrés à ApiController
10. ApiController envoie en JSON avec json()

