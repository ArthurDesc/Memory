<?php
session_start();
require 'autoload.php';

// SEND TO HOME PAGE IF NOT CONNECTED
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Exemple de création de cartes avec leurs images spécifiques
$card1 = new Card(1, './assets/pictures/cards/card1.png');
$card2 = new Card(2, './assets/pictures/cards/card2.png');
// Et ainsi de suite pour toutes les cartes...

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
// Exemple d'affichage des cartes
$cards = [$card1, $card2]; // Liste des cartes à afficher

?>

<!DOCTYPE html>
<html lang="fr">
<?php include './includes/_head.php'; ?>
<body>
    <div class="container">
        <h1>Jeu de Memory</h1>
        <div class="card-container">
            <?php
            foreach ($cards as $card) {
                // Utilisation d'un élément <img> pour chaque carte
                echo '<div class="card" style="width: 10vw; height: 15vw;">';
                echo '<img src="./assets/pictures/cards/recto.png" data-card-id="' . $card->getId() . '" alt="Card" class="card-front" style="width: 100%; height: 100%;">';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
