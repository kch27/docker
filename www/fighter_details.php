<?php
session_start();
require 'db_connection.php';

if (!isset($_GET['id'])) {
    header('Location: fighters.php');
    exit();
}

$id = (int)$_GET['id'];

$query = "SELECT fighters.*, categories.name AS category_name 
          FROM fighters 
          JOIN categories ON fighters.category_id = categories.id 
          WHERE fighters.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$fighter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fighter) {
    echo "<h2 class='error-message'>Combattant introuvable.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($fighter['name']); ?> - Détails</title>
    <link rel="stylesheet" href="css/style_fighter_details.css">
</head>
<body>
    <div class="container detail-container">
        <h1><?= htmlspecialchars($fighter['name']); ?></h1>
        <div class="fighter-detail-card">
            <div class="image-section">
                <img src="<?= htmlspecialchars($fighter['image']); ?>" alt="<?= htmlspecialchars($fighter['name']); ?>">
            </div>
            <div class="info-section">
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($fighter['category_name']); ?></p>
                <p><strong>Âge :</strong> <?= $fighter['age']; ?> ans</p>
                <p><strong>Taille :</strong> <?= $fighter['height_cm']; ?> cm</p>
                <p><strong>Poids :</strong> <?= $fighter['weight_kg']; ?> kg</p>
                <p><strong>Nationalité :</strong> <?= htmlspecialchars($fighter['nationality']); ?></p>
                <p><strong>Palmarès :</strong> 
                    <span class="palmares"><?= $fighter['wins']; ?> - <?= $fighter['losses']; ?> - <?= $fighter['draws']; ?></span>
                </p>
                <p><strong>Biographie :</strong><br> <?= nl2br(htmlspecialchars($fighter['bio'])); ?></p>
                <a href="fighters.php" class="btn">← Retour</a>
            </div>
        </div>
    </div>
</body>
</html>
