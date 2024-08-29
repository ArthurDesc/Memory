<?php
require './autoload.php';
class User {
    private $id;
    public $username;
    public $password; // Ce champ ne sera pas exposé directement pour des raisons de sécurité

    public function __construct($username = "", $password = "") {
        $this->id = null;
        $this->username = $username;
        $this->password = $password;
    }

    // Méthode pour l'enregistrement
    public function register($pdo, $password) {
        // Vérifier si le username existe déjà
        $checkQuery = "SELECT id FROM users WHERE username = :username";
        $stmt = $pdo->prepare($checkQuery);

        $stmt->bindValue(':username', $this->username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return "Le nom d'utilisateur est déjà utilisé.";
        }

        // Si le username n'existe pas encore, continuer avec l'insertion
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($query);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':password', $hashed_password);

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour la connexion
    public function connect($pdo, $username, $password) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);

        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour la déconnexion
    public function disconnect() {
        $this->id = null;
        $this->username = "";
        $this->password = "";
    }

    // Méthode pour supprimer le compte utilisateur
    public function delete($pdo) {
        if ($this->id !== null) {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $pdo->prepare($query);

            $stmt->bindValue(':id', $this->id);

            if ($stmt->execute()) {
                $this->disconnect(); // Déconnecter l'utilisateur après la suppression
                return true;
            }
        }
        return false;
    }

    // Méthode pour mettre à jour les informations utilisateur
    public function update($pdo, $new_username, $new_password) {
        if ($this->id !== null) {
            $query = "UPDATE users SET username = :username, password = :password WHERE id = :id";
            $stmt = $pdo->prepare($query);

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt->bindValue(':username', $new_username);
            $stmt->bindValue(':password', $hashed_password);
            $stmt->bindValue(':id', $this->id);

            if ($stmt->execute()) {
                // Mettre à jour les attributs de l'objet après une mise à jour réussie
                $this->username = $new_username;
                $this->password = $new_password;
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // Vérifier si l'utilisateur est connecté
    public function isConnected() {
        return $this->id !== null;
    }

    // Obtenir l'ID de l'utilisateur
    public function getId() {
        return $this->id;
    }

    // Obtenir le nom d'utilisateur
    public function getUsername() {
        return $this->username;
    }
}
?>
