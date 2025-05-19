<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="/css/style_index.css">
    <style>
        /* Correction pour l'overlay */
        body::before {
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur le site de Muay Thai</h1>
        <p class="description">
            Connectez-vous ou inscrivez-vous pour explorer les combattants et gérer les données.
        </p>
        <div class="button-container">
            <a href="connexion.php" class="btn">Se connecter</a>
            <a href="register.php" class="btn btn-secondary">S'inscrire</a>
        </div>
    </div>
</body>
</html>
