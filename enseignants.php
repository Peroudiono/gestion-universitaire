<?php
include('connexion.php');
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}
$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];


    if ($role !== 'admin') {
        header("Location: unauthorized.php"); 
        exit();
    }
}
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

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <br />
            <input type="text" id="inputSearch" placeholder="Rechercher ..." class="form-control  pull-right" style="width: 300px; display: inline-block; margin-right: 30px;">
            <a href="ajouter_enseignant.php" class="btn btn-success btn-fill pull-left" style="margin-left: 0.80cm;"><i class="fas fa-plus"></i> Ajouter un nouveau enseignant</a>

            <br />

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Département</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
                                            <th>Actions</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlfetsh = "SELECT * FROM `enseignant` order by id asc";
                                            $res = $conn->query($sqlfetsh);
                                            while ($row = $res->fetch_assoc()) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $row['nom']; ?></td>
                                                    <td><?php echo $row['prenom']; ?></td>
                                                    <td><?php echo $row['departement']; ?></td>
                                                    <td><?php echo $row['numero_telephone']; ?></td>
                                                    <td><?php echo $row['adresse']; ?></td>
                                                    <td>
                                                        <a href="modifier_enseignant.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-fill"><i class="fas fa-pencil-alt"></i></a>
                                                        <button type="button" class="btn btn-danger btn-fill" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="pe-7s-trash"></i></button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action supprimera définitivement cet enseignant !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui !',
            cancelButtonText: 'Non',
            width: '40%',
            customClass: {
                container: 'my-swal'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "supprimer_enseignant.php?id=" + id;
            }
        });
    }
</script>

</html>