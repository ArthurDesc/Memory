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

// Connexion à la base de données via la classe Database
<?php
// ... (le reste du code reste inchangé)

try {
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

    // Le reste du code pour insérer/mettre à jour le score

    // Après l'insertion/mise à jour, vérifiez si l'opération a réussi
    $affectedRows = $stmt->rowCount();
    if ($affectedRows == 0) {
        throw new Exception("No rows were affected by the insert/update operation");
    }

    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Attempts saved: $attempts, Affected rows: $affectedRows\n", FILE_APPEND);
    echo json_encode(['status' => 'success', 'message' => 'Attempts saved: ' . $attempts]);

} catch (Exception $e) {
    file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}


file_put_contents('debug_save_attempts.log', date('Y-m-d H:i:s') . " - Script ended\n", FILE_APPEND);
?>