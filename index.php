    <?php
    session_start();
    require './autoload.php';

    // Initialiser les variables de message
    $error = '';
    $success = '';

    // Connexion à la base de données (ajoute cette ligne si elle manque)
    try {
        $conn = new PDO('mysql:host=localhost;dbname=memory', 'root', ''); // Adapté à tes paramètres de connexion
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Connexion échouée : ' . $e->getMessage());
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Récupérer les données du formulaire avec une valeur par défaut si la clé n'existe pas
$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';


        // Créer une instance de la classe User
        $user = new User();

        // Tenter de se connecter
        $result = $user->connect($conn, $login, $password);

        if ($result === true) {
            // Connexion réussie, rediriger vers une autre page
            $_SESSION['user'] = serialize($user); // Sérialiser l'objet User
            header('Location: home.php');
            exit();
        } else {
            // Connexion échouée, afficher un message d'erreur
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
    ?>


    <!DOCTYPE html>
    <html lang="fr">
    <?php include './includes/_head.php'; ?>

    <body>
        <div class="form-container">
            <h2>Connexion</h2>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="login">Nom d'utilisateur</label>
                    <input type="text" id="login" name="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Se connecter</button>
            </form>
            Vous n'avez pas de compte ? <a href="register.php">Inscription</a>
        </div>
    </body>
    </html>
