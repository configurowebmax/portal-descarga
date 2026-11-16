<?php
include('../conn/conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileID = $_POST['fileID'];
    $fileTitle = trim($_POST['fileTitle']);
    $fileUploader = trim($_POST['fileUploader']);
    $fileUploader = !empty($fileUploader) ? $fileUploader : NULL;

    // Verificar si se ha cargado un nuevo archivo
    if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
        // No se ha cargado un nuevo archivo, actualizar solo los demás campos
        $stmt = $conn->prepare("UPDATE tbl_file SET file_title = :fileTitle, file_uploader = :fileUploader WHERE tbl_file_id = :fileID");
        $stmt->bindParam(':fileTitle', $fileTitle);
        $stmt->bindParam(':fileUploader', $fileUploader);
        $stmt->bindParam(':fileID', $fileID);
    } else {
        // Se ha cargado un nuevo archivo
        $file = $_FILES['file']['name'];
        $target_dir = "../file-uploads/";
        $target_file = $target_dir . basename($file);

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE tbl_file SET file_title = :fileTitle, file = :file, file_uploader = :fileUploader WHERE tbl_file_id = :fileID");
            $stmt->bindParam(':fileTitle', $fileTitle);
            $stmt->bindParam(':file', $file);
            $stmt->bindParam(':fileUploader', $fileUploader);
            $stmt->bindParam(':fileID', $fileID);
        } else {
            // Manejar el error si el archivo no se pudo mover
            die("Error al subir el archivo.");
        }
    }

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("Location: ../index.php");
    } else {
        die("Error al actualizar el archivo.");
    }
}
