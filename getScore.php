<?php
require 'autoload.php'; // Inclure toutes les classes nécessaires

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Requête SQL pour récupérer les pseudos et les scores moyens
    $stmt = $pdo->prepare("
        SELECT 
            u.username AS pseudo, 
            ROUND(AVG(g.moves), 2) AS average_score
        FROM 
            users u
        JOIN 
            games g ON u.id = g.user_id
        GROUP BY 
            u.id, u.username
        ORDER BY 
            average_score DESC
    ");
    $stmt->execute();
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($scores);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
