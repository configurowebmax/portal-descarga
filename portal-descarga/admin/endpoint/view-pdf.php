<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['tbl_user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el nombre del archivo PDF de la URL
$file = isset($_GET['file']) ? $_GET['file'] : '';

if (!$file) {
    echo "Archivo no especificado.";
    exit();
}

// Verificar que el archivo existe
$filepath = "../ruta/a/tu/carpeta/de/archivos/" . $file;
if (!file_exists($filepath)) {
    echo "Archivo no encontrado.";
    exit();
}

// Mostrar el archivo PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $file . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
readfile($filepath);
