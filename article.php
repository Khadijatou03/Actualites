<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Article non spécifié");
}

$articleId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=mglsi_news", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$query = "SELECT a.titre, a.contenu, a.dateCreation, a.dateModification, c.libelle 
          FROM Article a 
          JOIN Categorie c ON a.categorie = c.id 
          WHERE a.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$articleId]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Article non trouvé");
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($article['titre']); ?></title>
    <meta charset="UTF-8">
</head>
<body>
    

    <div class="header">
        <h1>Dernières Actualités</h1>
    </div>

    <div class="article-content">
        <h2><?php echo htmlspecialchars($article['titre']); ?></h2>
        <p class="meta">
            Catégorie: <?php echo htmlspecialchars($article['libelle']); ?> | 
            Publié le: <?php echo date('d/m/Y H:i', strtotime($article['dateCreation'])); ?>
            <?php if ($article['dateModification'] != $article['dateCreation']): ?>
                | Modifié le: <?php echo date('d/m/Y H:i', strtotime($article['dateModification'])); ?>
            <?php endif; ?>
        </p>
        <div class="content">
            <?php echo nl2br(htmlspecialchars($article['contenu'])); ?>
        </div>
        <a href="index.php" class="back-link">Retour aux actualités</a>
    </div>
</body>
</html>