<?php
require_once './includes/session.php';
require 'autoload.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

// Désérialiser l'objet User depuis la session
$user = unserialize($_SESSION['user']);

// Vérifier la connexion
if (!$user->isConnected()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

// Lire les données envoyées par la requête AJAX
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);
$attempts = isset($data['attempts']) ? intval($data['attempts']) : 0;
$pairsCount = isset($data['pairsCount']) ? intval($data['pairsCount']) : 3;

try {
    // Connexion à la base de données via la classe Database
    $db = new Database();
    $pdo = $db->getConnection();

    $userId = $user->getId();

    // Vérifier si l'ID de l'utilisateur existe
    $checkUserStmt = $pdo->prepare("SELECT id FROM users WHERE id = :user_id");
    $checkUserStmt->execute(['user_id' => $userId]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userExists) {
        throw new Exception("User ID does not exist in the database");
    }

    // Créer un nouvel enregistrement pour chaque partie
    $sql = "INSERT INTO games (user_id, moves, pairs_count) VALUES (:user_id, :moves, :pairs_count)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $userId, 
        'moves' => $attempts,
        'pairs_count' => $pairsCount
    ]);

    // Vérifier si l'insertion a réussi
    $affectedRows = $stmt->rowCount();
    if ($affectedRows == 0) {
        throw new Exception("No rows were affected by the insert operation");
    }

    // Calculer la moyenne des essais pour l'utilisateur
    $stmt = $pdo->prepare("SELECT AVG(moves) AS average_moves FROM games WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $averageMoves = $result['average_moves'];
    
    // Renvoyer la réponse avec la moyenne des essais
    echo json_encode(['status' => 'success', 'message' => 'Attempts saved: ' . $attempts, 'average_moves' => $averageMoves]);

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
