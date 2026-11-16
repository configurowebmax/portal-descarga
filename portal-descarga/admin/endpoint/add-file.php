<?php
include('../conn/conn.php');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM tbl_users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$username = $user['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileTitle = trim($_POST['fileTitle']);
    $fileUploader = $username; // Usar el nombre de usuario logueado
    $file = $_FILES['file'];
    $status = 'pending'; // Estado inicial
    $pre_published = false; // Estado inicial de prepublicación

    // Verificar que el archivo se ha subido correctamente
    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $targetFilePath = "../file-uploads/" . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // Insertar detalles del archivo en tbl_file
            $stmt = $conn->prepare("INSERT INTO tbl_file (file_title, file, file_uploader, status, pre_published) VALUES (:fileTitle, :file, :fileUploader, :status, :pre_published)");
            $stmt->bindParam(':fileTitle', $fileTitle);
            $stmt->bindParam(':file', $fileName);
            $stmt->bindParam(':fileUploader', $fileUploader);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':pre_published', $pre_published);
            $stmt->execute();

            $fileId = $conn->lastInsertId(); // Obtener el ID del archivo recién insertado

            // Si el archivo es marcado como producto, procesar los campos adicionales
            if (isset($_POST['isProduct']) && $_POST['isProduct'] == 'on') {
                $description = trim($_POST['productDescription']);
                $productImage = $_FILES['productImage'];
                $price = $_POST['productPrice'];

                // Verificar que la imagen del producto se ha subido correctamente
                if ($productImage['error'] == UPLOAD_ERR_OK) {
                    $productImageName = basename($productImage['name']);
                    $targetProductImagePath = "../product-images/" . $productImageName;

                    if (move_uploaded_file($productImage['tmp_name'], $targetProductImagePath)) {
                        // Insertar detalles del producto en tbl_product
                        $stmtProduct = $conn->prepare("INSERT INTO tbl_product (file_id, description, image, price) VALUES (:file_id, :description, :image, :price)");
                        $stmtProduct->bindParam(':file_id', $fileId);
                        $stmtProduct->bindParam(':description', $description);
                        $stmtProduct->bindParam(':image', $productImageName);
                        $stmtProduct->bindParam(':price', $price);
                        $stmtProduct->execute();
                    } else {
                        echo "Error al subir la imagen del producto.";
                    }
                } else {
                    echo "Error al procesar la imagen del producto.";
                }
            }

            header("Location: ../index.php");
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Error al procesar el archivo.";
    }
}
