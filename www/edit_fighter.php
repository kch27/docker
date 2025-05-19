<?php
session_start();
require_once 'db_connection.php';

// Vérification de la session utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérification du rôle admin
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

// Récupérer les infos du combattant
$query = "SELECT * FROM fighters WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$fighter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fighter) {
    echo "Combattant introuvable.";
    exit();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $age = $_POST["age"];
    $height_cm = $_POST["height_cm"];
    $weight_kg = $_POST["weight_kg"];
    $category_id = $_POST["category_id"];
    $image_url = $_POST["image_url"];

    // Gérer l'upload de l'image ou l'URL fournie
    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES["image"]["name"];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    } elseif (!empty($image_url)) {
        $image = $image_url; // Utiliser l'URL fournie
    } else {
        $image = $fighter['image']; // Conserver l'image actuelle
    }

    // Mettre à jour dans la base de données
    $queryUpdate = "UPDATE fighters SET name = :name, age = :age, height_cm = :height_cm, weight_kg = :weight_kg, category_id = :category_id, image = :image WHERE id = :id";
    $stmtUpdate = $pdo->prepare($queryUpdate);
    $stmtUpdate->bindParam(':name', $name);
    $stmtUpdate->bindParam(':age', $age);
    $stmtUpdate->bindParam(':height_cm', $height_cm);
    $stmtUpdate->bindParam(':weight_kg', $weight_kg);
    $stmtUpdate->bindParam(':category_id', $category_id);
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
    <title>Modifier un Combattant</title>
    <link rel="stylesheet" href="/css/style_edit_fighter.css">
</head>
<body>
    <h2>Modifier le Combattant</h2>
    <form action="edit_fighter.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
        <label>Nom :</label>
        <input type="text" name="name" value="<?= htmlspecialchars($fighter['name']); ?>" required><br>

        <label>Âge :</label>
        <input type="number" name="age" value="<?= $fighter['age']; ?>" required><br>

        <label>Taille (cm) :</label>
        <input type="number" name="height_cm" value="<?= $fighter['height_cm']; ?>" required><br>

        <label>Poids (kg) :</label>
        <input type="number" name="weight_kg" value="<?= $fighter['weight_kg']; ?>" required><br>

        <label>Catégorie :</label>
        <input type="number" name="category_id" value="<?= $fighter['category_id']; ?>" required><br>

        <label>Image actuelle :</label><br>
        <?php if (filter_var($fighter['image'], FILTER_VALIDATE_URL)): ?>
            <img src="<?= $fighter['image']; ?>" width="100">
        <?php else: ?>
            <img src="uploads/<?= $fighter['image']; ?>" width="100">
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
