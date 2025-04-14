<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

include('connexion.php');

$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];

    if ($role !== 'etudiant') {
        header("Location: unauthorized.php"); 
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('index.css'); ?>
    <style>
        .swal2-popup {
            font-size: 14px !important;
        }

        .modal-backdrop.in {
            opacity: 0 !important;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <br />

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <th>Cours</th>
                                            <th>Date d'ajout</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlfetsh = "SELECT * FROM `cours` ORDER BY id ASC";
                                            $res = $conn->query($sqlfetsh);
                                            while ($row = $res->fetch_assoc()) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $row['titre']; ?></td>
                                                    <td><?php echo $row['date_creation']; ?></td>
                                                    <td>
                                                        <a href="download.php?file=<?php echo $row['cours']; ?>" class="btn btn-primary btn-fill" target="_blank"><i class="pe-7s-download" style="font-size: 24px;"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
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

</body>
<?php include('index.js'); ?>

</html>