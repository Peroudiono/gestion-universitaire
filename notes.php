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
    $id_utilisateur = $row['id'];
    $role = $row['role']; // Récupérer le rôle de l'utilisateur
} else {
    header("Location: unauthorized.php"); // Redirection si l'utilisateur n'est pas trouvé
    exit();
}

// Récupérer les notes en fonction du rôle
if ($role === 'etudiant') {
    // Récupérer les notes de l'étudiant
    $sql_notes = "SELECT c.titre AS cours, n.note FROM notes n
                  JOIN cours c ON n.id_cours = c.id
                  WHERE n.id_etudiant = $id_utilisateur";
} elseif ($role === 'enseignant') {
    // Récupérer les notes de tous les étudiants dans les cours de l'enseignant
    $sql_notes = "SELECT c.titre AS cours, n.note, a.prenom, a.nom FROM notes n
                  JOIN cours c ON n.id_cours = c.id
                  JOIN inscriptions i ON n.id_etudiant = i.student_id
                  JOIN accounts a ON i.student_id = a.id
                  WHERE c.id_enseignant = (SELECT id FROM enseignant WHERE email='$email_connecte')";
} elseif ($role === 'admin') {
    // Récupérer toutes les notes pour l'administrateur
    $sql_notes = "SELECT c.titre AS cours, n.note, a.prenom, a.nom FROM notes n
                  JOIN cours c ON n.id_cours = c.id
                  JOIN inscriptions i ON n.id_etudiant = i.student_id
                  JOIN accounts a ON i.student_id = a.id";
} else {
    header("Location: unauthorized.php"); // Redirection pour un rôle non autorisé
    exit();
}

$result_notes = $conn->query($sql_notes);
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
                    <h4><strong>Mes Notes</strong></h4>
                    <div class="row">
                        <?php
                        if ($result_notes->num_rows > 0) {
                            while ($row_note = $result_notes->fetch_assoc()) {
                                $cours = $row_note['cours'];
                                $note = $row_note['note'];
                                $etudiant = isset($row_note['prenom']) ? $row_note['prenom'] . ' ' . $row_note['nom'] : '';
                        ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title" style="text-align: center;"><strong><?php echo $cours; ?></strong></h5>
                                            <p class="card-text">Note : <?php echo $note; ?></p>
                                            <?php if ($role === 'enseignant') : ?>
                                                <p class="card-text">Étudiant : <?php echo $etudiant; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<div class='col-md-12'><p>Aucune note trouvée.</p></div>";
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