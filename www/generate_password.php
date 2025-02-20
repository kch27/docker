<?php
// Nouveau mot de passe
$new_password = 'admin'; // Remplacez par votre nouveau mot de passe

// Génération du hash avec la méthode BCRYPT
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Affichage du hash pour que vous puissiez le copier
echo $hashed_password;
?>
