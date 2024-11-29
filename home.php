<?php
require_once './includes/session.php';
require 'autoload.php';

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

$error = '';
$success = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer le nombre de paires sélectionnées
    $pairs_count = intval($_POST['pairs_count']);

    // Validation du nombre de paires
    if ($pairs_count < 3 || $pairs_count > 12) {
        $error = 'Le nombre de paires doit être entre 3 et 12.';
    } else {
        // Stocker le nombre de paires dans la session
        $_SESSION['pairs_count'] = $pairs_count;
        // Rediriger vers la page du jeu
        header('Location: game.php');
        exit();
    }
}

try {
    // Utiliser la classe Database existante pour obtenir la connexion PDO
    $db = new Database();
    $pdo = $db->getConnection();

    // Requête pour récupérer le classement des joueurs
    $stmt = $pdo->prepare("
        SELECT 
            u.username AS pseudo,
            COUNT(g.id) as parties_jouees,
            ROUND(AVG(1000 / ((g.pairs_count * g.pairs_count) * g.moves)), 2) AS score_moyen
        FROM 
            users u
        LEFT JOIN 
            games g ON u.id = g.user_id
        GROUP BY 
            u.id, u.username
        HAVING 
            parties_jouees > 0
        ORDER BY 
            score_moyen DESC  -- DESC car maintenant un score plus haut est meilleur
        LIMIT 10
    ");
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération du classement : " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="fr">
<?php include './includes/_head.php'; ?>

<body>
    <main>
        <h1>Bienvenue, <?php echo htmlspecialchars($user->getUsername()); ?> !</h1>
        <p>Choisissez le nombre de paires de cartes pour commencer le Memory :</p>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="pairs_count">Nombre de cartes :</label>
                <select id="pairs_count" name="pairs_count" required>
                    <option value="3">6</option>
                    <option value="4">8</option>
                    <option value="5">10</option>
                    <option value="6">12</option>
                    <option value="7">14</option>
                    <option value="8">16</option>
                    <option value="9">18</option>
                    <option value="10">20</option>
                    <option value="11">22</option>
                    <option value="12">24</option>
                </select>
            </div>
            <button type="submit">Lancer la partie</button>
        </form>
        <form action="index.php" method="POST">
            <button type="submit">Déconnexion</button>
        </form>

        <div id="leaderboard-container">
            <p>🏆 Classement des joueurs</p>
            <table>
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Joueur</th>
                        <th>Score moyen</th>
                        <th>Parties jouées</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $player): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($player['pseudo']); ?></td>
                            <td><?php echo $player['score_moyen']; ?></td>
                            <td><?php echo $player['parties_jouees']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>

</html>