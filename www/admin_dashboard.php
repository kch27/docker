<?php

session_start();
require_once 'db_connection.php';

// Vérification de la session utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérification du rôle admin
$isAdmin = false;
$queryRole = "SELECT role FROM users WHERE id = :user_id";
$stmtRole = $pdo->prepare($queryRole);
$stmtRole->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtRole->execute();
$userRole = $stmtRole->fetch(PDO::FETCH_ASSOC);

if ($userRole && $userRole['role'] === 'admin') {
    $isAdmin = true;
}

// Gestion des catégories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];
    $query = "INSERT INTO categories (name, description) VALUES (:name, :description)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $category_name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
}

// Ajouter un combattant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fighter'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $height_cm = $_POST['height_cm'];
    $weight_kg = $_POST['weight_kg'];
    $category_id = $_POST['category_id'];

    // Gérer l'image (upload ou URL)
    if (!empty($_POST['image_url'])) {
        $image = $_POST['image_url']; // Utiliser l'URL fournie
    } else {
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image = $target; // Chemin de l'image téléchargée
    }

    $query = "INSERT INTO fighters (name, age, height_cm, weight_kg, category_id, image) 
              VALUES (:name, :age, :height_cm, :weight_kg, :category_id, :image)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':height_cm', $height_cm);
    $stmt->bindParam(':weight_kg', $weight_kg);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image', $image);
    $stmt->execute();
}

// Activer/Désactiver un utilisateur
if (isset($_GET['toggle_user_id'])) {
    $user_id = $_GET['toggle_user_id'];
    $query = "UPDATE users SET active = NOT active WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit();
}

// Récupérer les catégories
$queryCategories = "SELECT * FROM categories";
$stmtCategories = $pdo->query($queryCategories);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les utilisateurs
$queryUsers = "SELECT * FROM users";
$stmtUsers = $pdo->query($queryUsers);
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les combattants
$queryFighters = "SELECT fighters.*, categories.name AS category_name FROM fighters JOIN categories ON fighters.category_id = categories.id";
$stmtFighters = $pdo->query($queryFighters);
$fighters = $stmtFighters->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Admin</title>
    <link rel="stylesheet" href="/css/style_admin_dash.css">
</head>
<body>
    <h2>Bienvenue dans l'interface Admin</h2>
    <a href="logout.php">Se déconnecter</a>
    <a href="fighters.php">Retour à la page client</a>

    <h3>Gestion des catégories</h3>
    <form method="POST" action="admin_dashboard.php">
        <label>Nom de la catégorie :</label>
        <input type="text" name="category_name" required>
        <label>Description :</label>
        <textarea name="description"></textarea>
        <button type="submit" name="add_category">Ajouter Catégorie</button>
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= $category['id']; ?></td>
                    <td><?= htmlspecialchars($category['name']); ?></td>
                    <td><?= htmlspecialchars($category['description']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Gestion des utilisateurs</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id']; ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= $user['active'] ? 'Actif' : 'Inactif'; ?></td>
                    <td>
                        <a href="admin_dashboard.php?toggle_user_id=<?= $user['id']; ?>">
                            <?= $user['active'] ? 'Désactiver' : 'Activer'; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Ajouter un combattant</h3>
    <form method="POST" action="admin_dashboard.php" enctype="multipart/form-data">
        <label>Nom :</label>
        <input type="text" name="name" required>
        <label>Âge :</label>
        <input type="number" name="age" required>
        <label>Taille (cm) :</label>
        <input type="number" name="height_cm" required>
        <label>Poids (kg) :</label>
        <input type="number" name="weight_kg" required>
        <label>Catégorie :</label>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Image (Télécharger ou URL) :</label><br>
        <!-- Champ pour l'upload de l'image -->
        <input type="file" name="image"><br>
        <!-- Champ pour l'URL de l'image -->
        <input type="text" name="image_url" placeholder="Ou entrez un lien d'image"><br>
        <button type="submit" name="add_fighter">Ajouter Combattant</button>
    </form>

    <h3>Gestion des combattants</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Âge</th>
                <th>Taille</th>
                <th>Poids</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fighters as $fighter): ?>
                <tr>
                    <td><?= $fighter['id']; ?></td>
                    <td><?= htmlspecialchars($fighter['name']); ?></td>
                    <td><?= htmlspecialchars($fighter['category_name']); ?></td>
                    <td><?= $fighter['age']; ?> ans</td>
                    <td><?= $fighter['height_cm']; ?> cm</td>
                    <td><?= $fighter['weight_kg']; ?> kg</td>
                    <td>
                        <a href="edit_fighter.php?id=<?= $fighter['id']; ?>">Modifier</a> |
                        <a href="delete_fighter.php?id=<?= $fighter['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce combattant ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
