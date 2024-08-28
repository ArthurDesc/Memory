<?php
require_once 'classes/game.php';
require_once 'classes/leaderboard.php';
require_once 'classes/player.php';
require_once 'classes/card.php';
require_once 'db.php';
session_start();




// Si l'utilisateur est déjà connecté, redirigez-le vers la page du jeu
if (isset($_SESSION['user_id'])) {
    header('Location: game.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifiez si l'utilisateur existe dans la base de données
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Vérifiez le mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Stocker l'ID de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Rediriger vers la page du jeu
        header('Location: game.php');
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Memory Game</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <div class="register">
            Vous n'êtes pas déjà inscrit ? <a href="./register.php">S'inscrire ici</a>
        </div>
    </div>
</body>
</html>
