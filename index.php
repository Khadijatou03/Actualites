<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mglsi_news", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer toutes les catégories
    $queryCategories = "SELECT * FROM Categorie ORDER BY libelle";
    $stmtCategories = $pdo->query($queryCategories);
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

    // Préparer la requête des articles avec filtre de catégorie optionnel
    $query = "SELECT a.id, a.titre, a.contenu, a.dateCreation, c.libelle as categorie_nom 
              FROM Article a 
              JOIN Categorie c ON a.categorie = c.id";
    
    if (isset($_GET['categorie']) && is_numeric($_GET['categorie'])) {
        $query .= " WHERE a.categorie = :categorie";
    }
    $query .= " ORDER BY a.dateCreation DESC";

    $stmt = $pdo->prepare($query);
    
    if (isset($_GET['categorie']) && is_numeric($_GET['categorie'])) {
        $stmt->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Actualités MGLSI</title>
    <meta charset="UTF-8">
</head>
<body>
    <div class="header">
        <h1>Dernières Actualités</h1>
    </div>

    <div class="categories-nav">
        <ul class="categories-list">
            <li><a href="index.php" <?php echo !isset($_GET['categorie']) ? 'class="active"' : ''; ?>>Toutes</a></li>
            <?php foreach ($categories as $categorie): ?>
                <li>
                    <a href="index.php?categorie=<?php echo $categorie['id']; ?>" 
                       <?php echo (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id']) ? 'class="active"' : ''; ?>>
                        <?php echo htmlspecialchars($categorie['libelle']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if (empty($articles)): ?>
        <p style="text-align: center; padding: 20px;">Aucune actualité disponible pour le moment.</p>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-preview">
                    <h3><?php echo htmlspecialchars($article['titre']); ?></h3>
                    <p class="meta">
                        Catégorie: <?php echo htmlspecialchars($article['categorie_nom']); ?> | 
                        Publié le: <?php echo date('d/m/Y H:i', strtotime($article['dateCreation'])); ?>
                    </p>
                    <p>
                        <?php 
                        $preview = substr($article['contenu'], 0, 100) . '...'; 
                        echo htmlspecialchars($preview); 
                        ?>
                    </p>
                    <a href="article.php?id=<?php echo $article['id']; ?>" class="read-more">Lire la suite</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>