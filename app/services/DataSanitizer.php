<?php
// app/services/DataSanitizer.php
namespace App\Services;

/**
 * Classe DataSanitizer
 * 
 * Responsabilité: Nettoyer les données entrantes
 */
class DataSanitizer {
    public static function cleanText($value) {
        return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
    
    public static function cleanInt($value) {
        return (int)($value ?? 0);
    }
    
    public static function cleanFloat($value) {
        return (float)($value ?? 0);
    }
    
    public static function cleanCheckbox($value) {
        return isset($value) ? 1 : 0;
    }
    
    public static function cleanTime($value) {
        return $value ?? '';
    }
}
