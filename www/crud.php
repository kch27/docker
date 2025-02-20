<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Ajouter un utilisateur
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (username, password, active) VALUES ('$username', '$password', 1)";
    $conn->query($sql);
}

// Modifier un utilisateur
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "UPDATE users SET username='$username', password='$password' WHERE id=$id";
    $conn->query($sql);
}

// Activer/Désactiver un utilisateur
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $currentStatus = $conn->query("SELECT active FROM users WHERE id=$id")->fetch_assoc()['active'];
    $newStatus = $currentStatus ? 0 : 1;
    $conn->query("UPDATE users SET active=$newStatus WHERE id=$id");
}

// Récupérer tous les utilisateurs
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs</title>
</head>
<body>
    <h2>Bienvenue, <?= $_SESSION['admin'] ?> !</h2>
    <a href="logout.php">Déconnexion</a>

    <h2>Liste des utilisateurs</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['active'] ? 'Actif' : 'Inactif' ?></td>
                <td>
                    <a href="?toggle=<?= $row['id'] ?>" onclick="return confirm('Changer le statut de cet utilisateur ?');">
                        <?= $row['active'] ? 'Désactiver' : 'Activer' ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Ajouter un utilisateur</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="add">Ajouter</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
