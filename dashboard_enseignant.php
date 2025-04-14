<?php
include('connexion.php');
session_start();

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email_connecte = $_SESSION['email']; 
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email_connecte'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];

    // Vérification que l'utilisateur est un enseignant
    if ($role !== 'enseignant') {
        header("Location: unauthorized.php");
        exit();
    }
} else {
    header("Location: unauthorized.php"); // Si l'utilisateur n'est pas trouvé
    exit();
}

// Récupérer l'ID de l'enseignant
$sql_enseignant = "SELECT id FROM enseignant WHERE email='$email_connecte'";
$result_enseignant = $conn->query($sql_enseignant);
if ($result_enseignant->num_rows > 0) {
    $row_enseignant = $result_enseignant->fetch_assoc();
    $enseignant_id = $row_enseignant['id'];
} else {
    header("Location: unauthorized.php");
    exit();
}

// Récupérer les cours de l'enseignant
$query_cours = "SELECT * FROM cours WHERE id_enseignant = '$enseignant_id'";
$result_cours = $conn->query($query_cours);

// Si un formulaire a été soumis pour ajouter ou modifier une absence ou une note
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'ajouter') {
        // Ajouter une absence ou une note
        $student_id = $_POST['student_id'];
        $absence = $_POST['absence'];  // Absence donnée en nombre d'heures ou comme "présent" ou "absent"
        $note = $_POST['note'];        // Note de l'étudiant
        $course_id = $_POST['course_id']; // ID du cours

        // Insérer dans la base de données
        $query_insert = "INSERT INTO absences_notes (student_id, course_id, absence, note) 
                         VALUES ('$student_id', '$course_id', '$absence', '$note')";
        $conn->query($query_insert);
    }

    // Si l'action est de modifier
    if (isset($_POST['action']) && $_POST['action'] == 'modifier') {
        $id = $_POST['id'];  // ID d'absence ou de note à modifier
        $absence = $_POST['absence'];  // Nouvelle absence
        $note = $_POST['note'];        // Nouvelle note

        // Modifier l'absence et la note
        $query_update = "UPDATE absences_notes SET absence='$absence', note='$note' WHERE id='$id'";
        $conn->query($query_update);
    }

    // Si l'action est de supprimer
    if (isset($_POST['action']) && $_POST['action'] == 'supprimer') {
        $id = $_POST['id'];  // ID de l'absence ou de la note à supprimer

        // Supprimer l'absence ou la note
        $query_delete = "DELETE FROM absences_notes WHERE id='$id'";
        $conn->query($query_delete);
    }
}
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
                                    <h3>Bienvenue, <?php echo $row['prenom']; ?> <?php echo $row['nom']; ?> !</h3>

                                    <h4>Gestion des Absences et Notes</h4>

                                    <form action="" method="POST">
                                        <input type="hidden" name="action" value="ajouter">
                                        <select name="student_id" required>
                                            <?php 
                                                // Récupérer les étudiants inscrits dans le cours
                                                $query_etudiants = "SELECT a.id, a.nom, a.prenom FROM accounts a
                                                                    JOIN inscriptions i ON a.id = i.student_id
                                                                    WHERE i.course_id = ?";
                                                $stmt = $conn->prepare($query_etudiants);
                                                $stmt->bind_param("i", $course_id);
                                                $stmt->execute();
                                                $result_etudiants = $stmt->get_result();

                                                while ($student = $result_etudiants->fetch_assoc()) {
                                                    echo "<option value='" . $student['id'] . "'>" . $student['prenom'] . " " . $student['nom'] . "</option>";
                                                }
                                            ?>
                                        </select>
                                        <input type="number" name="absence" placeholder="Absences" required>
                                        <input type="number" name="note" placeholder="Note" required>
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>"> <!-- Ajout du course_id -->
                                        <button type="submit">Ajouter Absence / Note</button>
                                    </form>

                                    <h5>Absences et Notes</h5>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Etudiant</th>
                                                <th>Absence</th>
                                                <th>Note</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // Afficher les absences et notes des étudiants
                                                $query_absences_notes = "SELECT * FROM absences_notes WHERE course_id='$course_id'";
                                                $result_absences_notes = $conn->query($query_absences_notes);
                                                
                                                while ($row = $result_absences_notes->fetch_assoc()) {
                                                    echo "<tr>
                                                            <td>" . $row['student_id'] . "</td>
                                                            <td>" . $row['absence'] . "</td>
                                                            <td>" . $row['note'] . "</td>
                                                            <td>
                                                                <form action='' method='POST'>
                                                                    <input type='hidden' name='action' value='modifier'>
                                                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                                    <input type='number' name='absence' value='" . $row['absence'] . "'>
                                                                    <input type='number' name='note' value='" . $row['note'] . "'>
                                                                    <button type='submit'>Modifier</button>
                                                                </form>
                                                                <form action='' method='POST'>
                                                                    <input type='hidden' name='action' value='supprimer'>
                                                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                                    <button type='submit'>Supprimer</button>
                                                                </form>
                                                            </td>
                                                        </tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>



    <div class="dashboard-links">
    <a class="button" href="gestion_absences.php">Gérer les absences</a>
    <a class="button" href="gestion_notes.php">Gérer les notes</a>
   </div>





</body>

<?php include('index.js'); ?>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Cours', 'Etudiants'],
            datasets: [{
                label: 'Mes Statistiques',
                data: [<?php echo $total_cours; ?>, <?php echo $total_etudiants; ?>],
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


