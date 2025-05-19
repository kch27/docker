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

// Récupérer les infos du plat
$query = "SELECT * FROM plats WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$plat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plat) {
    echo "Plat introuvable.";
    exit();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $prix = $_POST["prix"];
    $stock = $_POST["stock"];
    $image_url = $_POST["image_url"];

    // Gérer l'upload de l'image ou l'URL fournie
    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES["image"]["name"];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    } elseif (!empty($image_url)) {
        $image = $image_url; // Utiliser l'URL fournie
    } else {
        $image = $plat['image']; // Conserver l'image actuelle
    }

    // Mettre à jour dans la base de données
    $queryUpdate = "UPDATE plats SET titre = :titre, description = :description, prix = :prix, stock = :stock, image = :image WHERE id = :id";
    $stmtUpdate = $pdo->prepare($queryUpdate);
    $stmtUpdate->bindParam(':titre', $titre);
    $stmtUpdate->bindParam(':description', $description);
    $stmtUpdate->bindParam(':prix', $prix);
    $stmtUpdate->bindParam(':stock', $stock);
    $stmtUpdate->bindParam(':image', $image);
    $stmtUpdate->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtUpdate->execute();

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Plat</title>
    <link rel="stylesheet" href="css/style_edit_plat.css">
</head>
<body>
    <h2>Modifier le Plat</h2>
    <form action="modifier_plat.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
        <label>Titre :</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($plat['titre']); ?>" required><br>

        <label>Description :</label>
        <textarea name="description" required><?= htmlspecialchars($plat['description']); ?></textarea><br>

        <label>Prix :</label>
        <input type="number" name="prix" step="0.01" value="<?= $plat['prix']; ?>" required><br>

        <label>Stock :</label>
        <input type="number" name="stock" value="<?= $plat['stock']; ?>" required><br>

        <label>Image actuelle :</label><br>
        <?php if (filter_var($plat['image'], FILTER_VALIDATE_URL)): ?>
            <img src="<?= $plat['image']; ?>" width="100">
        <?php else: ?>
            <img src="uploads/<?= $plat['image']; ?>" width="100">
        <?php endif; ?>
        <br>

        <label>Nouvelle image (Upload) :</label>
        <input type="file" name="image"><br>

        <label>Ou entrer une URL d’image :</label>
        <input type="url" name="image_url" placeholder="https://exemple.com/image.jpg"><br>

        <button type="submit">Mettre à jour</button>
    </form>
    <a href="admin_dashboard.php">Retour</a>
</body>
</html>
