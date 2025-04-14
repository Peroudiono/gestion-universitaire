<?php
include('connexion.php');
session_start();


if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}
$email_connecte = $_SESSION['email']; 
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email_connecte'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];


    if ($role !== 'admin') {
        header("Location: unauthorized.php"); 
        exit();
    }
}
// Récupérer les données à partir de la base de données
$query_etudiants = "SELECT COUNT(*) as total_etudiants FROM accounts WHERE role='etudiant'";
$result_etudiants = $conn->query($query_etudiants);

$row_etudiants = mysqli_fetch_assoc($result_etudiants);
$total_etudiants = $row_etudiants['total_etudiants'];



$query_enseignants = "SELECT COUNT(*) as total_enseignants FROM enseignant";
$result_enseignants = $conn->query($query_enseignants);
$row_enseignants = mysqli_fetch_assoc($result_enseignants);
$total_enseignants = $row_enseignants['total_enseignants'];

$query_departements = "SELECT COUNT(*) as total_departements FROM departement";
$result_departements = $conn->query($query_departements);
$row_departements = mysqli_fetch_assoc($result_departements);
$total_departements = $row_departements['total_departements'];

$query_specialites = "SELECT COUNT(*) as total_specialites FROM specialite";
$result_specialites = $conn->query($query_specialites);
$row_specialites = mysqli_fetch_assoc($result_specialites);
$total_specialites = $row_specialites['total_specialites'];

$query_classes = "SELECT COUNT(*) as total_classes FROM classe";
$result_classes = $conn->query($query_classes);
$row_classes = mysqli_fetch_assoc($result_classes);
$total_classes = $row_classes['total_classes'];

$query_cours = "SELECT COUNT(*) as total_cours FROM cours";
$result_cours = $conn->query($query_cours);
$row_cours = mysqli_fetch_assoc($result_cours);
$total_cours = $row_cours['total_cours'];
?>
<!doctype html>
<html lang="en">

<head>
    <?php include('index.css'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>


            <div class="content">
                <div class="container-fluid">
                    <div class="row">


                        <div class="col-md-10">
                            <div class="card">

                                <div class="content">
                                    <canvas id="myChart"></canvas>

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

<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Etudiants', 'Enseignants', 'Départements', 'Spécialités', 'Classes', 'Cours'],
            datasets: [{
                label: ' ',
                data: [<?php echo $total_etudiants; ?>, <?php echo $total_enseignants; ?>, <?php echo $total_departements; ?>, <?php echo $total_specialites; ?>, <?php echo $total_classes; ?>, <?php echo $total_cours; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</html>