<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email_connecte = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email_connecte'";
$result_user = $conn->query($sql_fetch_user);
if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $id_etudiant = $row['id'];
}

// Récupérer les absences de l'étudiant
$sql_absences = "SELECT c.titre AS cours, a.date_absence, a.justifiee FROM absences a
                 JOIN cours c ON a.id_cours = c.id
                 WHERE a.id_etudiant = $id_etudiant";
$result_absences = $conn->query($sql_absences);
?>

<!doctype html>
<html lang="en">

<head>
    <?php include('index.css'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="card card-user">
                <div class="content">
                    <h4><strong>Mes Absences</strong></h4>
                    <div class="row">
                        <?php
                        if ($result_absences->num_rows > 0) {
                            while ($row_absence = $result_absences->fetch_assoc()) {
                                $cours = $row_absence['cours'];
                                $date_absence = $row_absence['date_absence'];
                                $justifiee = $row_absence['justifiee'] ? 'Justifiée' : 'Non Justifiée';
                        ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title" style="text-align: center;"><strong><?php echo $cours; ?></strong></h5>
                                            <p class="card-text">Date de l'absence : <?php echo $date_absence; ?></p>
                                            <p class="card-text">Justification : <?php echo $justifiee; ?></p>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<div class='col-md-12'><p>Aucune absence trouvée.</p></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>
</body>
<?php include('index.js'); ?>

</html>
