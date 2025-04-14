<?php
session_start();

if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

include('connexion.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Requête pour récupérer le mot de passe hashé et le rôle
    $sql = "SELECT mot_de_passe, role FROM accounts WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['mot_de_passe'];
        $role = $row['role'];

        // Vérification du mot de passe
        if (password_verify($mot_de_passe, $hashed_password)) {
            $_SESSION['email'] = $email;

            // Redirection selon le rôle de l'utilisateur
            if ($role === 'admin') {
                header("Location: dashboard.php"); // Tableau de bord admin
                exit();
            } elseif ($role === 'etudiant') {
                header("Location: accueil.php"); // Tableau de bord étudiant
                exit();
            } elseif ($role === 'enseignant') {
                header("Location: dashboard_enseignant.php"); // Tableau de bord enseignant
                exit();
            } else {
                $message = "Rôle non autorisé";
            }
        } else {
            $message = "Mot de passe incorrect";
        }
    } else {
        $message = "Email non trouvé";
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
    <br /><br />
    <div class="content">
        <div class="container-fluid" style="width: 80%; margin: 0 auto;">
            <?php if (!empty($message)) { ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                <script>
                    Swal.fire({
                        title: 'Message',
                        text: '<?php echo $message; ?>',
                        icon: 'info',
                        showConfirmButton: true,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    });
                </script>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="width: 70%; margin: 0 auto;">
                        <div class="header">
                            <h3 class="title" style="font-weight: bold; text-align: center;">Connexion</h3>
                        </div>
                        <div class="content">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Email :</label>
                                            <input type="email" class="form-control" name="email" id="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="mot_de_passe">Mot de passe :</label>
                                            <input type="password" class="form-control" name="mot_de_passe" id="mot_de_passe" required>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-success btn-fill" style="margin-left: 42%;">Connexion</button>
                                    <br /><br />
                                    <a href="inscription.php" style="margin-left: 38%;">Vous n'avez pas de compte ?</a>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('index.js'); ?>

</html>
