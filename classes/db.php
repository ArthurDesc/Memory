<?php
// Database.php

class Database {
    private $pdo;

    public function __construct() {
        $servername = "mysql";
        $username = "root";
        $password = "root";
        $database = "memory";

        try {
            // Créer une connexion PDO
            $this->pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
            
            // Configurer PDO pour afficher les erreurs sous forme d'exceptions
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Si une erreur survient, afficher un message et arrêter le script
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Méthode pour obtenir la connexion PDO
    public function getConnection() {
        return $this->pdo;
    }
}
?>
