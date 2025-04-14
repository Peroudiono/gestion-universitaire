<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    $email = $_SESSION['email'];

    // Récupérer le hachage du mot de passe actuel de la base de données
    $sql_fetch_password = "SELECT mot_de_passe FROM accounts WHERE email='$email'";
    $result = $conn->query($sql_fetch_password);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['mot_de_passe'];


        if (password_verify($old_password, $hashed_password)) {
            // Générer un nouveau hachage pour le mot de passe
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe dans la base de données
            $sql_update_password = "UPDATE accounts SET mot_de_passe='$new_hashed_password' WHERE email='$email'";
            if ($conn->query($sql_update_password) === TRUE) {
                $message = "Mot de passe modifié avec succès.";

                // Déconnexion de l'utilisateur
                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            } else {
                $message = "Erreur lors de la mise à jour du mot de passe: " . $conn->error;
            }
        } else {
            $message = "Ancien mot de passe incorrect.";
        }
    }
}
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

            <div class="content" style="width: 70%; margin: 0 auto;">
                <h2 style="text-align: center;">Modifier votre mot de passe</h2>
                <form method="post">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="old_password">Ancien mot de passe:</label>
                                <input type="password" class="form-control" name="old_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">Nouveau mot de passe:</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                        </div>



                    </div>



                    <div>
                        <button type="submit" class="btn btn-success btn-fill" style="margin-left: 42%;">Modifier le mot de passe</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>

</body>

<?php include('index.js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    <?php if (!empty($message)) { ?>
        Swal.fire({
            title: 'Message',
            text: '<?php echo $message; ?>',
            icon: '<?php echo ($message === "Mot de passe modifié avec succès.") ? "success" : "error" ?>',
            showConfirmButton: true,
            allowOutsideClick: false
        });
    <?php } ?>
</script>

</html>