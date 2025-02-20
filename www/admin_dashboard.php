<?php
session_start();
require_once 'db_connection.php'; // Inclure la connexion PDO

// Vérification de la session
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

// Activation / Désactivation d'un utilisateur
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $action = $_GET['action'];

    if ($action == 'activate') {
        $query = "UPDATE users SET active = 1 WHERE id = :user_id";
    } elseif ($action == 'deactivate') {
        $query = "UPDATE users SET active = 0 WHERE id = :user_id";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Récupérer la liste des utilisateurs
$query = "SELECT * FROM users";
$stmt = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Admin</title>
</head>
<body>
    <h2>Bienvenue dans l'interface d'administration</h2>
    <a href="logout.php">Se déconnecter</a>

    <h3>Liste des utilisateurs</h3>
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
            <?php while ($user = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['active'] == 1 ? 'Actif' : 'Désactivé'; ?></td>
                    <td>
                        <?php if ($user['active'] == 1): ?>
                            <a href="admin_dashboard.php?action=deactivate&user_id=<?php echo $user['id']; ?>">Désactiver</a>
                        <?php else: ?>
                            <a href="admin_dashboard.php?action=activate&user_id=<?php echo $user['id']; ?>">Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
