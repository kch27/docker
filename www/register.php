<?php
// Connexion à la base de données

require 'db_connection.php';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
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
            echo "Cet utilisateur existe déjà.";
        } else {
            // Insertion de l'utilisateur dans la base de données
            $insertQuery = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->bindParam(':username', $inputUsername);
            $insertStmt->bindParam(':password', $hashedPassword);
            $insertStmt->execute();

            echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    } else {
        echo "Les mots de passe ne correspondent pas.";
    }
}
?>
