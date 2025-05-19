<?php
// Connexion à la base de données
require 'db_connection.php';

$message = ""; // Variable pour stocker les messages d'erreur ou de succès

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = trim($_POST['username']);
    $inputPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérification que les mots de passe correspondent
    if ($inputPassword === $confirmPassword) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($inputPassword, PASSWORD_BCRYPT);

        // Vérification que l'utilisateur n'existe pas déjà
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $inputUsername);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $message = "Cet utilisateur existe déjà.";
        } else {
            // Insertion de l'utilisateur dans la base de données
            $insertQuery = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->bindParam(':username', $inputUsername);
            $insertStmt->bindParam(':password', $hashedPassword);
            if ($insertStmt->execute()) {
                $message = "Inscription réussie ! <a href='connexion.php'>Connectez-vous ici</a>.";
            } else {
                $message = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    } else {
        $message = "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/style_inscrire.css">
</head>
<body>
    <div class="form-container">
        <h1>Créer un compte</h1>
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

            <label for="confirm_password">Confirmez le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>

            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a>.</p>
    </div>
</body>
</html>
