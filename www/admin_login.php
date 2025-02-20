<?php
session_start();
require_once 'db_connection.php'; // Inclure la connexion PDO

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour vérifier l'utilisateur admin
    $query = "SELECT * FROM admins WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id']; // Stocke l'id de l'admin dans la session
        header('Location: admin_dashboard.php'); // Redirige vers la page d'administration
        exit();
    } else {
        $error_message = "Identifiants incorrects!";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
</head>
<body>
    <h2>Connexion à l'interface Admin</h2>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" required><br><br>
        
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
