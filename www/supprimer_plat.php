<?php
session_start();
require_once 'db_connection.php';

// Vérification de la session admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$id = $_GET['id'];

// Supprimer l'image du dossier uploads
$queryImage = "SELECT image FROM plats WHERE id = :id";
$stmtImage = $pdo->prepare($queryImage);
$stmtImage->bindParam(':id', $id, PDO::PARAM_INT);
$stmtImage->execute();
$plat = $stmtImage->fetch(PDO::FETCH_ASSOC);

if ($plat && file_exists("uploads/" . $plat['image'])) {
    unlink("uploads/" . $plat['image']);
}

// Supprimer le plat de la base de données
$queryDelete = "DELETE FROM plats WHERE id = :id";
$stmtDelete = $pdo->prepare($queryDelete);
$stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
$stmtDelete->execute();

header("Location: admin_dashboard.php");
exit();
?>
