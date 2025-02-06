<?php
// Connexion BDD
require 'db_connection.php';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Vérification des informations
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $inputUsername);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($inputPassword, $user['password'])) {
        echo "Connexion réussie ! Bienvenue, " . htmlspecialchars($user['username']) . "!";
    } else {
        echo "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
