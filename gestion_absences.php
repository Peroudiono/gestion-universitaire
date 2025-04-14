<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $id_enseignant = $row['id'];
}

// Vérification des droits d'accès : administrateur ou enseignant
if ($role !== 'admin' && $role !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

// Récupérer tous les étudiants
$sql_fetch_students = "SELECT * FROM accounts WHERE role='etudiant'";
$result_students = $conn->query($sql_fetch_students);

// Récupérer toutes les absences pour tous les étudiants
$sql_fetch_absences = "SELECT absences.id, absences.id_cours, absences.date_absence, absences.justifiee, cours.titre AS nom_cours 
                       FROM absences
                       JOIN cours ON absences.id_cours = cours.id";

$result_absences = $conn->query($sql_fetch_absences);

// Traitement des ajouts et suppressions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_absence'])) {
        $id_etudiant = $_POST['id_etudiant'];
        $id_cours = $_POST['id_cours'];
        $date_absence = $_POST['date_absence'];
        $justifiee = isset($_POST['justifiee']) ? 1 : 0;

        $sql_add_absence = "INSERT INTO absences (id_etudiant, id_cours, date_absence, justifiee) 
                            VALUES ('$id_etudiant', '$id_cours', '$date_absence', '$justifiee')";
        echo $conn->query($sql_add_absence) ? 'Absence ajoutée.' : 'Erreur.';
    } elseif (isset($_POST['supprimer_absence'])) {
        $absence_id = $_POST['absence_id'];

        $sql_delete_absence = "DELETE FROM absences WHERE id = '$absence_id'";
        echo $conn->query($sql_delete_absence) ? 'Absence supprimée.' : 'Erreur.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Absences</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .form-container {
            margin-bottom: 30px;
        }
        .form-container select, .form-container input[type="date"], .form-container input[type="checkbox"] {
            padding: 8px;
            margin-right: 10px;
        }
        .form-container label {
            margin-right: 10px;
        }
        .back-btn {
            margin-top: 20px;
            text-align: center;
        }
        .back-btn button {
            background-color: #007BFF;
            padding: 10px 20px;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gestion des Absences</h1>
        
        <!-- Formulaire d'ajout d'absence -->
        <div class="form-container">
            <form method="POST">
                <h2>Ajouter une Absence</h2>
                <select name="id_etudiant" required>
                    <option value="">Sélectionner un étudiant</option>
                    <?php while ($student = $result_students->fetch_assoc()) { ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['nom']; ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="date_absence" required>
                <select name="id_cours" required>
                    <option value="">Sélectionner un cours</option>
                    <?php
                    // Récupérer tous les cours disponibles
                    $sql_courses = "SELECT * FROM cours";
                    $result_courses = $conn->query($sql_courses);
                    while ($course = $result_courses->fetch_assoc()) {
                        echo "<option value='{$course['id']}'>{$course['titre']}</option>";
                    }
                    ?>
                </select>
                <label for="justifiee">Absence justifiée:</label>
                <input type="checkbox" name="justifiee" value="1">
                <button type="submit" name="ajouter_absence">Ajouter Absence</button>
            </form>
        </div>

        <!-- Liste des absences -->
        <h2>Liste des Absences</h2>
        <table>
            <tr>
                <th>Cours</th>
                <th>Date</th>
                <th>Justifiée</th>
                <th>Actions</th>
            </tr>
            <?php while ($absence = $result_absences->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $absence['nom_cours']; ?></td>
                    <td><?php echo $absence['date_absence']; ?></td>
                    <td><?php echo $absence['justifiee'] ? 'Oui' : 'Non'; ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="absence_id" value="<?php echo $absence['id']; ?>">
                            <button type="submit" name="supprimer_absence">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <!-- Bouton de retour -->
        <div class="back-btn">
            <button onclick="window.history.back();">Retour</button>
        </div>
    </div>
</body>

</html>