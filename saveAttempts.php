<?php
session_start();
require 'autoload.php'; // Assurez-vous que ce fichier inclut Database.php et autres nécessaires

// Log pour le débogage
file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - User not authenticated\n", FILE_APPEND);
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

// Désérialiser l'objet User depuis la session
$user = unserialize($_SESSION['user']);

// Vérifier la connexion
if (!$user->isConnected()) {
    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - User not connected\n", FILE_APPEND);
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

// Lire les données envoyées par la requête AJAX
$rawData = file_get_contents('php://input');
file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Raw data received: " . $rawData . "\n", FILE_APPEND);

$data = json_decode($rawData, true);
$attempts = isset($data['attempts']) ? intval($data['attempts']) : 0;
file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Decoded attempts: " . $attempts . "\n", FILE_APPEND);

try {
    // Connexion à la base de données via la classe Database
    $db = new Database();
    $pdo = $db->getConnection();

    $userId = $user->getId();
    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - User ID: " . $userId . "\n", FILE_APPEND);

    // Vérifier si l'ID de l'utilisateur existe dans la table users
    $checkUserStmt = $pdo->prepare("SELECT id FROM users WHERE id = :user_id");
    $checkUserStmt->execute(['user_id' => $userId]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userExists) {
        throw new Exception("User ID does not exist in the database");
    }

    // Vérifier s'il existe déjà un enregistrement pour l'utilisateur
    $stmt = $pdo->prepare("SELECT id FROM games WHERE user_id = :user_id ORDER BY played_at DESC LIMIT 1");
    $stmt->execute(['user_id' => $userId]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        // Mettre à jour le nombre d'essais
        $stmt = $pdo->prepare("UPDATE games SET moves = :moves WHERE id = :id");
        $stmt->execute(['moves' => $attempts, 'id' => $game['id']]);
    } else {
        // Créer un nouvel enregistrement si aucun n'existe
        $stmt = $pdo->prepare("INSERT INTO games (user_id, moves) VALUES (:user_id, :moves)");
        $stmt->execute(['user_id' => $userId, 'moves' => $attempts]);
    }

    // Vérifier si l'opération d'insertion/mise à jour a réussi
    $affectedRows = $stmt->rowCount();
    if ($affectedRows == 0) {
        throw new Exception("No rows were affected by the insert/update operation");
    }

    // Calculer la moyenne des essais pour l'utilisateur
    $stmt = $pdo->prepare("SELECT AVG(moves) AS average_moves FROM games WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $averageMoves = $result['average_moves'];

    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Attempts saved: $attempts, Affected rows: $affectedRows, Average moves: $averageMoves\n", FILE_APPEND);
    
    // Renvoyer la réponse avec la moyenne des essais
    echo json_encode(['status' => 'success', 'message' => 'Attempts saved: ' . $attempts, 'average_moves' => $averageMoves]);

} catch (Exception $e) {
    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Script ended\n", FILE_APPEND);
?>
