<?php
/**
 * Script d'initialisation de la base de données Mobile Money
 * Exécutez ce script avec: php setup_db.php
 */

require __DIR__ . '/vendor/autoload.php';

$database = __DIR__ . '/writable/database.sqlite';
$sql = file_get_contents(__DIR__ . '/base.sql');

try {
    // Créer le fichier de base de données s'il n'existe pas
    if (!file_exists($database)) {
        touch($database);
    }
    
    $pdo = new PDO('sqlite:' . $database);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Exécuter les commandes SQL une par une
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Base de données créée avec succès!\n";
    echo "Fichier: " . $database . "\n";
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
