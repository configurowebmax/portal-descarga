<?php
session_start();  // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tbl_customer_id'])) {
    header("Location: ./registro-usuario/index.php");
    exit();
}

include('./admin/conn/conn.php');

if (isset($_GET['fileID'])) {
    $fileID = $_GET['fileID'];

    $stmt = $conn->prepare("SELECT file FROM tbl_file WHERE tbl_file_id = :fileID AND status = 'approved'");
    $stmt->bindParam(':fileID', $fileID, PDO::PARAM_INT);
    $stmt->execute();
    $fileData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fileData) {
        $filePath = './admin/file-uploads/' . $fileData['file'];
        if (file_exists($filePath)) {
            header("Content-Type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . basename($fileData['file']) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit();
        } else {
            echo "El archivo no existe.";
        }
    } else {
        echo "Archivo no encontrado o no aprobado.";
    }
} else {
    echo "ID de archivo no especificado.";
}
