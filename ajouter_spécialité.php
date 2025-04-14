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
// Sélection des départements depuis la base de données
$sql_departements = "SELECT id, nom_departement FROM departement";
$result_departements = $conn->query($sql_departements);

$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom_specialite = $conn->real_escape_string($_POST['nom_specialite']);
    $description = $conn->real_escape_string($_POST['description']);
    $departement = $conn->real_escape_string($_POST['departement']);


    $sql = "INSERT INTO specialite (nom_specialite, description, departement, date_de_creation) VALUES ('$nom_specialite', '$description', '$departement', NOW())";


    if ($conn->query($sql) === TRUE) {
        $message = "La spécialité a été ajouté avec succès!";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
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
                                window.location.href = 'spécialités.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-plus" style="margin-right: 10px;"></i>Ajouter une nouvelle spécialité</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nom_specialite">Nom du spécialité :</label>
                                                    <input type="text" class="form-control" name="nom_specialite" id="nom_specialite" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description du spécialité :</label>
                                                    <textarea class="form-control" name="description" id="description" rows="5" required></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="departement">Département :</label>
                                                    <select class="form-control" name="departement" id="departement" required>
                                                        <?php
                                                        while ($row = $result_departements->fetch_assoc()) {
                                                            echo "<option value='" . $row['nom_departement'] . "'>" . $row['nom_departement'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>



                                        <div>
                                            <a href="spécialités.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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