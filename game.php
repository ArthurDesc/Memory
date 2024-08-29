<?php
session_start();
require 'autoload.php';

// SEND TO HOME PAGE IF NOT CONNECTED
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Désérialiser l'objet User depuis la session
$user = unserialize($_SESSION['user']);

// Vérifier la connexion
if (!$user->isConnected()) {
    header('Location: index.php');
    exit();
}

// Récupérer le nombre de paires depuis la session
$pairs_count = isset($_SESSION['pairs_count']) ? intval($_SESSION['pairs_count']) : 0;

// Créer une instance de Structure pour le jeu
$game = new Structure($pairs_count);

// Obtenir les cartes pour affichage
$cards = $game->getCards();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu de Memory</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Jeu de Memory</h1>
        <div class="card-container">
            <?php foreach ($cards as $card): ?>
                <div class="card" style="background-image: url('./assets/pictures/cards/<?php echo htmlspecialchars($card->getImage()); ?>');">
                    <!-- Vous pouvez ajouter du contenu supplémentaire ici si nécessaire -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
