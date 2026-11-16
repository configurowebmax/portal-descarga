<?php
include('../conn/conn.php');

try {
    $fileID = $_GET['fileID'];
    $stmt = $conn->prepare("SELECT * FROM `tbl_file` WHERE tbl_file_id = :fileID");
    $stmt->bindParam(':fileID', $fileID);
    $stmt->execute();
    $fileData = $stmt->fetch();

    if ($fileData) {
        // Obtén la ruta del archivo desde la base de datos
        $file_path = '../file-uploads/' . $fileData['file']; // Asegúrate de que esta ruta sea correcta
        $file_name = $fileData['file']; // Nombre del archivo para la descarga

        if (file_exists($file_path)) {
            header("Content-Type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Content-Length: ' . filesize($file_path));

            // Limpia el búfer de salida y desactiva el almacenamiento en búfer de salida
            ob_clean();
            flush();

            // Lee el archivo y envíalo al buffer de salida
            readfile($file_path);
            exit;
        } else {
            echo 'File not found.';
        }
    } else {
        echo 'File not found.';
    }
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage();
}
