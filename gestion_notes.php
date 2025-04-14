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

// Traitement des ajouts, modifications, ou suppressions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_note'])) {
        $id_etudiant = $_POST['id_etudiant'];
        $id_cours = $_POST['id_cours'];
        $note = $_POST['note'];

        // Ajouter la note sans restriction
        $sql_add_note = "INSERT INTO notes (id_etudiant, id_cours, note, date_creation) 
                         VALUES ('$id_etudiant', '$id_cours', '$note', NOW())";
        echo $conn->query($sql_add_note) ? 'Note ajoutée.' : 'Erreur.';
    } elseif (isset($_POST['modifier_note'])) {
        $note_id = $_POST['note_id'];
        $new_note = $_POST['new_note'];

        // Modifier la note sans restriction
        $sql_update_note = "UPDATE notes SET note = '$new_note' WHERE id = '$note_id'";
        echo $conn->query($sql_update_note) ? 'Note modifiée.' : 'Erreur.';
    } elseif (isset($_POST['supprimer_note'])) {
        $note_id = $_POST['note_id'];

        // Supprimer la note sans restriction
        $sql_delete_note = "DELETE FROM notes WHERE id = '$note_id'";
        echo $conn->query($sql_delete_note) ? 'Note supprimée.' : 'Erreur.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            margin: 10px 0;
        }

        .button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        td input[type="number"], td input[type="text"] {
            width: 80px;
            padding: 5px;
            text-align: center;
        }

        td form {
            display: inline-block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input[type="number"], .form-group select {
            padding: 8px;
            width: 200px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gestion des Notes</h1>

        <!-- Bouton Retour -->
        <button class="button" onclick="window.history.back();">Retour</button>

        <form method="POST">
            <h2>Ajouter une Note</h2>
            <div class="form-group">
                <select name="id_etudiant" required>
                    <option value="">Sélectionner un étudiant</option>
                    <?php while ($student = $result_students->fetch_assoc()) { ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['nom']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
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
            </div>
            <div class="form-group">
                <input type="number" name="note" placeholder="Note" required>
            </div>
            <button type="submit" name="ajouter_note" class="button">Ajouter Note</button>
        </form>

        <h2>Liste des Notes</h2>
        <table>
            <tr>
                <th>Cours</th>
                <th>Étudiant</th>
                <th>Note</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
            <?php 
            // Récupérer toutes les notes
            $sql_check_notes = "SELECT notes.id, notes.note, notes.date_creation, cours.titre AS nom_cours, accounts.nom AS nom_etudiant
                                 FROM notes
                                 JOIN cours ON notes.id_cours = cours.id
                                 JOIN accounts ON notes.id_etudiant = accounts.id";
            $result_notes = $conn->query($sql_check_notes);

            while ($note = $result_notes->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $note['nom_cours']; ?></td>
                    <td><?php echo $note['nom_etudiant']; ?></td>
                    <td><?php echo $note['note']; ?></td>
                    <td><?php echo $note['date_creation']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <input type="number" name="new_note" value="<?php echo $note['note']; ?>" required>
                            <button type="submit" name="modifier_note" class="button">Modifier</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <button type="submit" name="supprimer_note" class="button">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</body>

</html>