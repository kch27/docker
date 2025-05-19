<?php
session_start();
require_once 'db_connection.php';

// Vérification de la session utilisateur et du rôle admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$queryRole = "SELECT role FROM users WHERE id = :user_id";
$stmtRole = $pdo->prepare($queryRole);
$stmtRole->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtRole->execute();
$userRole = $stmtRole->fetch(PDO::FETCH_ASSOC);

if (!$userRole || $userRole['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$id = $_GET['id'];

// Supprimer l'image associée au combattant
$queryImage = "SELECT image FROM fighters WHERE id = :id";
$stmtImage = $pdo->prepare($queryImage);
$stmtImage->bindParam(':id', $id, PDO::PARAM_INT);
$stmtImage->execute();
$fighter = $stmtImage->fetch(PDO::FETCH_ASSOC);

if ($fighter && !empty($fighter['image']) && file_exists($fighter['image']) && !filter_var($fighter['image'], FILTER_VALIDATE_URL)) {
    unlink($fighter['image']);
}

// Supprimer le combattant de la base de données
$queryDelete = "DELETE FROM fighters WHERE id = :id";
$stmtDelete = $pdo->prepare($queryDelete);
$stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
$stmtDelete->execute();

header("Location: admin_dashboard.php");
exit();
?>
