<?php
session_start();

require 'autoload.php'; // Assure-toi que ce fichier inclut Database.php et autres nécessaires

// Vérifier si l'utilisateur est connecté
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $attempts = isset($data['attempts']) ? intval($data['attempts']) : 0;
    $userId = $user->getId(); // Récupérer l'ID de l'utilisateur depuis la session

    try {
        // Connexion à la base de données via la classe Database
        $db = new Database(); // Création d'une instance de la classe Database
        $pdo = $db->getConnection(); // Obtention de la connexion PDO

        // Requête pour mettre à jour le nombre d'essais
        $stmt = $pdo->prepare("UPDATE games SET moves = :moves WHERE user_id = :user_id ORDER BY played_at DESC LIMIT 1");
        $stmt->execute(['moves' => $attempts, 'user_id' => $userId]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    exit(); // Sortir pour éviter d'afficher la page HTML
}

// Lire le nombre de paires depuis la session
$pairs_count = isset($_SESSION['pairs_count']) ? intval($_SESSION['pairs_count']) : 3; // Valeur par défaut si non défini
?>

<!DOCTYPE html>
<html lang="fr">
<?php include './includes/_head.php'; ?>
<body>
    <a href="home.php" class="logo">Accueil</a>
    <button onclick="location.reload();">Relancer la partie</button>
    <div class="container">
        <h1>Jeu de Memory</h1>
        <div class="attempts-display" id="attempts-display">Nombre d'essais : 0</div>

        <div class="card-container" id="card-container"></div>

        <!-- Importer les classes JavaScript -->
        <script type="module" src="./jsClass/card.js"></script>
        <script type="module" src="./jsClass/structure.js"></script>
        <script type="module">
            import { Structure } from './jsClass/structure.js'; // Assure-toi que le chemin est correct

            // Passer le nombre de paires au JavaScript
            const pairsCount = <?php echo $pairs_count; ?>;

            // Créer une instance de Structure avec le nombre de paires
            new Structure(pairsCount);
        </script>
    </div>
</body>
</html>
