<?php
include('connexion.php');
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']); 
$is_departement_page = strpos($current_page, 'département') !== false;
$is_specialite_page = strpos($current_page, 'spécialité') !== false;
$is_classe_page = strpos($current_page, 'classe') !== false;
$is_liste_cours_page = strpos($current_page, 'cours') !== false;
$is_etudiant_page = strpos($current_page, 'étudiant') !== false;
$is_role_page = strpos($current_page, 'role') !== false;
$is_enseignant_page = strpos($current_page, 'enseignant') !== false;
$is_matiere_page = strpos($current_page, 'matière') !== false;
$is_actualite_page = strpos($current_page, 'actualité') !== false;
$is_parametre_page = strpos($current_page, 'paramètre') !== false;
$is_liste_etudiants_page = $current_page === 'liste_etudiants.php';
$is_rapport_page = strpos($current_page, 'rapport') !== false;
$is_notes_page = strpos($current_page, 'gestion_notes.php') !== false; // Vérification de la page "Gestion Notes"
$is_absences_page = strpos($current_page, 'gestion_absences.php') !== false; // Vérification de la page "Gestion Absences"

$email = $_SESSION['email'];

$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $user_email = $row['email'];
    $nom = $row['nom'];
    $prenom = $row['prenom'];
}
?>

<div class="sidebar" data-color="purple">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text">
                <?php echo $nom; ?> <?php echo $prenom; ?>
            </a>
        </div>

        <ul class="nav">
            <?php if ($role === 'admin'): ?>
                <li <?php echo ($current_page == 'dashboard.php') ? 'class="active"' : ''; ?>>
                    <a href="dashboard.php">
                        <i class="pe-7s-home"></i>
                        <p>Accueil</p>
                    </a>
                </li>
                <li <?php echo $is_departement_page ? 'class="active"' : ''; ?>>
                    <a href="départements.php">
                        <i class="pe-7s-wallet"></i>
                        <p>Départements</p>
                    </a>
                </li>
                <li <?php echo $is_specialite_page ? 'class="active"' : ''; ?>>
                    <a href="spécialités.php">
                        <i class="pe-7s-credit"></i>
                        <p>Spécialités</p>
                    </a>
                </li>
                <li <?php echo $is_classe_page ? 'class="active"' : ''; ?>>
                    <a href="classes.php">
                        <i class="pe-7s-diskette"></i>
                        <p>Classes</p>
                    </a>
                </li>
                <li <?php echo $is_liste_cours_page ? 'class="active"' : ''; ?>>
                    <a href="liste_cours.php">
                        <i class="pe-7s-copy-file"></i>
                        <p>Cours</p>
                    </a>
                </li>
                <li <?php echo $is_etudiant_page ? 'class="active"' : ''; ?>>
                    <a href="étudiants.php">
                        <i class="pe-7s-user"></i>
                        <p>Etudiants</p>
                    </a>
                </li>
                <li <?php echo $is_enseignant_page ? 'class="active"' : ''; ?>>
                    <a href="enseignants.php">
                        <i class="pe-7s-users"></i>
                        <p>Enseignants</p>
                    </a>
                </li>
                <li <?php echo $is_matiere_page ? 'class="active"' : ''; ?>>
                    <a href="matières.php">
                        <i class="pe-7s-albums"></i>
                        <p>Matières</p>
                    </a>
                </li>
                <li <?php echo $is_actualite_page ? 'class="active"' : ''; ?>>
                    <a href="actualités.php">
                        <i class="pe-7s-note2"></i>
                        <p>Actualités</p>
                    </a>
                </li>
                <li <?php echo $is_role_page ? 'class="active"' : ''; ?>>
                    <a href="roles.php">
                        <i class="pe-7s-lock"></i>
                        <p>Gestion de rôles</p>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-id"></i>
                        <p>Modifier Mot de passe</p>
                    </a>
                </li>
                <li <?php echo $is_parametre_page || $is_liste_etudiants_page ? 'class="active"' : ''; ?>>
                    <a href="paramètres.php">
                        <i class="pe-7s-tools"></i>
                        <p>Paramètres</p>
                    </a>
                </li>

                <li <?php echo $is_notes_page ? 'class="active"' : ''; ?>>
                    <a href="gestion_notes.php"> <!-- Modifié ici -->
                        <i class="pe-7s-pen"></i>
                        <p>Notes</p>
                    </a>
                </li>
                <li <?php echo $is_absences_page ? 'class="active"' : ''; ?>>
                    <a href="gestion_absences.php"> <!-- Modifié ici -->
                        <i class="pe-7s-clock"></i>
                        <p>Absences</p>
                    </a>
                </li>

            <?php elseif ($role === 'etudiant'): ?>
                <li <?php echo ($current_page == 'accueil.php') ? 'class="active"' : ''; ?>>
                    <a href="accueil.php">
                        <i class="pe-7s-home"></i>
                        <p>Accueil</p>
                    </a>
                </li>
                <li <?php echo ($current_page == 'cours.php') ? 'class="active"' : ''; ?>>
                    <a href="cours.php">
                        <i class="pe-7s-copy-file"></i>
                        <p>Liste des cours</p>
                    </a>
                </li>
                <li <?php echo $is_notes_page ? 'class="active"' : ''; ?>>
                    <a href="notes.php">
                        <i class="pe-7s-pen"></i>
                        <p>Mes Notes</p>
                    </a>
                </li>
                <li <?php echo $is_absences_page ? 'class="active"' : ''; ?>>
                    <a href="absences.php">
                        <i class="pe-7s-clock"></i>
                        <p>Mes Absences</p>
                    </a>
                </li>
                <li <?php echo $is_rapport_page ? 'class="active"' : ''; ?>>
                    <a href="rapport.php">
                        <i class="pe-7s-file"></i>
                        <p>Rapport de stage</p>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-id"></i>
                        <p>Modifier Mot de passe</p>
                    </a>
                </li>

            <?php elseif ($role === 'enseignant'): ?>
                <li <?php echo ($current_page == 'accueil.php') ? 'class="active"' : ''; ?>>
                    <a href="accueil.php">
                        <i class="pe-7s-home"></i>
                        <p>Accueil</p>
                    </a>
                </li>
                <li <?php echo ($current_page == 'cours.php') ? 'class="active"' : ''; ?>>
                    <a href="cours.php">
                        <i class="pe-7s-copy-file"></i>
                        <p>Liste des cours</p>
                    </a>
                </li>
                <li <?php echo $is_notes_page ? 'class="active"' : ''; ?>>
                    <a href="gestion_notes.php"> <!-- Modifié ici -->
                        <i class="pe-7s-pen"></i>
                        <p>Notes</p>
                    </a>
                </li>
                <li <?php echo $is_absences_page ? 'class="active"' : ''; ?>>
                    <a href="gestion_absences.php"> <!-- Modifié ici -->
                        <i class="pe-7s-clock"></i>
                        <p>Absences</p>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-id"></i>
                        <p>Modifier Mot de passe</p>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="logout.php">
                    <i class="pe-7s-settings"></i>
                    <p>Déconnexion</p>
                </a>
            </li>
        </ul>
    </div>
</div>