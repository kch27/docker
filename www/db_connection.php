<?php
// Fichier : db_connection.php

// Connexion à la base de données
$host = 'db'; // Nom du conteneur MySQL
$dbname = 'login_system'; // Nom de la base de données
$username = 'user'; // Nom d'utilisateur
$password = 'user'; // Mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>