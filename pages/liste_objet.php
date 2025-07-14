<?php 
session_start();
include("../inc/connexion.php");


$categorie = isset($_GET['categorie']) ? mysqli_real_escape_string($conn, $_GET['categorie']) : "";


$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, e.date_retour, i.nom_image
        FROM objet o
        JOIN categorie_objet c ON o.id_categorie = c.id_categorie
        LEFT JOIN emprunt e ON o.id_objet = e.id_objet
        LEFT JOIN images_objet i ON o.id_objet = i.id_objet";

if ($categorie !== "") {
    $sql .= " WHERE c.nom_categorie = '$categorie'";
}

$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Liste des objets</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        .card-img-top {
            max-height: 180px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Liste des objets</h2>

        <form method="GET" class="mb-4">
            <div class="row g-2 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="categorie" class="col-form-label fw-bold">Catégorie :</label>
                </div>
                <div class="col-auto">
                    <select id="categorie" name="categorie" class="form-select">
                        <option value="">-- Toutes les catégories --</option>
                        <option value="Esthétique" <?= $categorie === 'Esthétique' ? 'selected' : '' ?>>Esthétique</option>
                        <option value="Bricolage" <?= $categorie === 'Bricolage' ? 'selected' : '' ?>>Bricolage</option>
                        <option value="Mécanique" <?= $categorie === 'Mécanique' ? 'selected' : '' ?>>Mécanique</option>
                        <option value="Cuisine" <?= $categorie === 'Cuisine' ? 'selected' : '' ?>>Cuisine</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($res)) : ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 text-center shadow-sm">
                        <?php 
                        $image_path = 'img/' . $row['nom_image'];
                        if (!empty($row['nom_image']) && file_exists($image_path)) {
                            echo '<img src="' . htmlspecialchars($image_path) . '" class="card-img-top" alt="' . htmlspecialchars($row['nom_objet']) . '">';
                        } else {
                            echo '<img src="img/default.jpg" class="card-img-top" alt="Image par défaut">';
                        }
                        ?>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title mt-2"><?= htmlspecialchars($row['nom_objet']) ?></h5>
                            <p class="text-muted mb-0"><?= htmlspecialchars($row['nom_categorie']) ?></p>
                        </div>
                        <div class="card-footer">
                            <?php if (!empty($row['date_retour'])) : ?>
                                <small class="text-danger">Emprunté jusqu’au <?= htmlspecialchars($row['date_retour']) ?></small>
                            <?php else : ?>
                                <small class="text-success">Disponible</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
