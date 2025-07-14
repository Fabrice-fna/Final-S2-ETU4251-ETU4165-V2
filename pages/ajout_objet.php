<?php
session_start();
include("../inc/connexion.php");

if (!isset($_SESSION['id_membre'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_objet = mysqli_real_escape_string($conn, $_POST['nom_objet']);
    $id_categorie = (int) $_POST['id_categorie'];
    $id_membre = $_SESSION['id_membre'];

    // 1. Insérer l’objet
    $sql = "INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES ('$nom_objet', $id_categorie, $id_membre)";
    if (mysqli_query($conn, $sql)) {
        $id_objet = mysqli_insert_id($conn);

        // 2. Gérer les images
        $upload_dir = "../assets/img/";
        $is_uploaded = false;

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $filename = basename($_FILES['images']['name'][$key]);
                $filepath = $upload_dir . $filename;
                if (move_uploaded_file($tmp_name, $filepath)) {
                    $filename_db = mysqli_real_escape_string($conn, $filename);
                    mysqli_query($conn, "INSERT INTO images_objet (id_objet, nom_image) VALUES ($id_objet, '$filename_db')");
                    $is_uploaded = true;
                }
            }
        }

        if (!$is_uploaded) {
            mysqli_query($conn, "INSERT INTO images_objet (id_objet, nom_image) VALUES ($id_objet, 'default.jpg')");
        }

        $message = "Objet ajouté avec succès !";
    } else {
        $message = "Erreur lors de l’ajout.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un objet</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="container mt-5">
    <h2>Ajouter un nouvel objet</h2>

    <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nom de l'objet</label>
            <input type="text" name="nom_objet" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Catégorie</label>
            <select name="id_categorie" class="form-select" required>
                <?php
                $cats = mysqli_query($conn, "SELECT * FROM categorie_objet");
                while ($cat = mysqli_fetch_assoc($cats)) {
                    echo "<option value='{$cat['id_categorie']}'>{$cat['nom_categorie']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Images (plusieurs possibles)</label>
            <input type="file" name="images[]" multiple class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</body>
</html>
