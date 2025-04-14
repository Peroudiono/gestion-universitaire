<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $filepath = '' . $file;

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo "Le fichier demandé n'existe pas. Chemin du fichier : " . $filepath;
    }
} else {
    echo "Paramètre de fichier manquant.";
}
