<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<h2 class='error-message'>Accès refusé. Veuillez vous connecter.</h2>";
    exit();
}

$queryCategories = "SELECT * FROM categories";
$stmtCategories = $pdo->query($queryCategories);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$items_par_page = 6;
$page_courante = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page_courante - 1) * $items_par_page;

$queryTotal = "SELECT COUNT(*) FROM fighters";
$total_items = $pdo->query($queryTotal)->fetchColumn();
$total_pages = ceil($total_items / $items_par_page);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$queryFighters = "SELECT fighters.*, categories.name AS category_name 
                  FROM fighters 
                  JOIN categories ON fighters.category_id = categories.id 
                  WHERE (:search = '' OR fighters.name LIKE :searchPattern)
                  AND (:category_id = 0 OR fighters.category_id = :category_id)
                  LIMIT :offset, :items_par_page";
$stmtFighters = $pdo->prepare($queryFighters);
$stmtFighters->bindValue(':search', $search, PDO::PARAM_STR);
$stmtFighters->bindValue(':searchPattern', "%$search%", PDO::PARAM_STR);
$stmtFighters->bindValue(':category_id', $category_id, PDO::PARAM_INT);
$stmtFighters->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmtFighters->bindValue(':items_par_page', $items_par_page, PDO::PARAM_INT);
$stmtFighters->execute();
$fighters = $stmtFighters->fetchAll(PDO::FETCH_ASSOC);

$current_script = htmlspecialchars($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fighters</title>
    <link rel="stylesheet" href="/css/style_restaurant.css">  
</head>
<body>
    <a href="logout.php" class="btn logout-btn">Se déconnecter</a>

    <div class="container">
        <h1>Fighters Directory</h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="btn">Interface Admin</a>
        <?php endif; ?>

        <form method="GET" action="fighters.php" class="search-form">
            <input type="text" name="search" placeholder="Rechercher un combattant..." value="<?= htmlspecialchars($search); ?>">
            <select name="category_id">
                <option value="0">Toutes les catégories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id']; ?>" <?= $category_id == $category['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Rechercher</button>
        </form>

        <div id="fighters" class="menu">
            <?php if (empty($fighters)): ?>
                <h2 class="no-fighters">Aucun combattant trouvé.</h2>
            <?php else: ?>
                <?php foreach ($fighters as $fighter): ?>
                    <div class="fighter">
                        <img src="<?= htmlspecialchars($fighter['image']); ?>" alt="<?= htmlspecialchars($fighter['name']); ?>">
                        <h3><?= htmlspecialchars($fighter['name']); ?></h3>
                        <p>Palmarès : <?= (int)$fighter['wins']; ?> - <?= (int)$fighter['losses']; ?> - <?= (int)$fighter['draws']; ?></p>
                        <a href="fighter_details.php?id=<?= $fighter['id']; ?>" class="btn">Voir détails</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <li class="page-item <?= ($page_courante == 1) ? "disabled" : "" ?>">
                <a href="<?= $current_script ?>?page=1" class="page-link">Première</a>
            </li>
            <li class="page-item <?= ($page_courante == 1) ? "disabled" : "" ?>">
                <a href="<?= $current_script ?>?page=<?= $page_courante - 1 ?>" class="page-link">Précédente</a>
            </li>

            <?php
            $pages_affichees = 4;
            $demi = floor($pages_affichees / 2);
            $debut = max(1, $page_courante - $demi);
            $fin = min($total_pages, $debut + $pages_affichees - 1);

            if ($fin - $debut + 1 < $pages_affichees) {
                $debut = max(1, $fin - $pages_affichees + 1);
            }

            for ($page = $debut; $page <= $fin; $page++): ?>
                <li class="page-item <?= ($page_courante == $page) ? "active" : "" ?>">
                    <a href="<?= $current_script ?>?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page_courante == $total_pages) ? "disabled" : "" ?>">
                <a href="<?= $current_script ?>?page=<?= $page_courante + 1 ?>" class="page-link">Suivante</a>
            </li>
            <li class="page-item <?= ($page_courante == $total_pages) ? "disabled" : "" ?>">
                <a href="<?= $current_script ?>?page=<?= $total_pages ?>" class="page-link">Dernière</a>
            </li>
        </div>
    </div>
</body>
</html>
