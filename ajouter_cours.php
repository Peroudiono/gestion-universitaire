<?php
include('connexion.php');
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}
$email = $_SESSION['email'];


$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];

    if ($role !== 'admin') {
        header("Location: unauthorized.php"); 
        exit();
    }
}
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titre = $_POST['titre'];

    if (isset($_FILES['cours']) && $_FILES['cours']['error'] === UPLOAD_ERR_OK) {
        $cours = $_FILES['cours'];


        $file_extension = pathinfo($cours['name'], PATHINFO_EXTENSION);
        if ($file_extension != 'pdf') {
            $message = "Veuillez télécharger un fichier PDF.";
        } else {
            $destination = 'pdf/' . $cours['name'];

            // Déplacer le fichier téléchargé vers le dossier "pdf"
            if (move_uploaded_file($cours['tmp_name'], $destination)) {

                $sql = "INSERT INTO cours (titre, cours, date_creation) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $titre, $destination);
                if ($stmt->execute()) {
                    $message = "Le cours a été ajouté avec succès!";
                } else {
                    echo "Erreur : " . $sql . "<br>" . $conn->error;
                }
                $stmt->close();
            } else {
                $message = "Erreur lors du téléchargement du fichier.";
            }
        }
    } else {
        $message = "Veuillez sélectionner un fichier PDF.";
    }
}

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <?php include('index.css'); ?>
    <style>
        .swal2-popup {
            font-size: 14px !important;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="content">
                <div class="container-fluid">

                    <?php if (!empty($message)) { ?>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                        <script>
                            Swal.fire({
                                title: 'Succès!',
                                text: '<?php echo $message; ?>',
                                icon: 'success',
                                showConfirmButton: true,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = 'liste_cours.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-plus" style="margin-right: 10px;"></i>Ajouter un nouveau cours</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="titre">Titre :</label>
                                                    <input type="text" class="form-control" name="titre" id="titre" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="cours">Cours :</label>
                                                    <input type="file" class="form-control" name="cours" id="cours" accept=".pdf" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <a href="liste_cours.php" class="btn btn-info btn-fill pull-right">Annuler</a>
                                            <button type="submit" class="btn btn-success btn-fill" style="margin-left: 82%;">Ajouter</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>

        </div>
    </div>

</body>

<?php include('index.js'); ?>

</html>