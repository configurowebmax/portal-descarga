<?php
include('./admin/conn/conn.php');
session_start(); // Iniciar la sesión para acceder y modificar variables de sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updateUserID = $_POST['tbl_user_id'];
    $updateFirstName = $_POST['first_name'];
    $updateLastName = $_POST['last_name'];
    $updateContactNumber = $_POST['contact_number'];
    $updateEmail = $_POST['email'];
    $updateUsername = $_POST['username'];
    $updatePassword = $_POST['password'];

    try {
        $conn->beginTransaction();

        // Si el campo de la contraseña está vacío, no actualizar la contraseña
        if (!empty($updatePassword)) {
            $hashedPassword = password_hash($updatePassword, PASSWORD_BCRYPT);
            $updateStmt = $conn->prepare("UPDATE `tbl_user` SET `first_name` = :first_name, `last_name` = :last_name, `contact_number` = :contact_number, `email` = :email, `username` = :username, `password` = :password WHERE `tbl_user_id` = :userID");
            $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        } else {
            $updateStmt = $conn->prepare("UPDATE `tbl_user` SET `first_name` = :first_name, `last_name` = :last_name, `contact_number` = :contact_number, `email` = :email, `username` = :username WHERE `tbl_user_id` = :userID");
        }

        $updateStmt->bindParam(':first_name', $updateFirstName, PDO::PARAM_STR);
        $updateStmt->bindParam(':last_name', $updateLastName, PDO::PARAM_STR);
        $updateStmt->bindParam(':contact_number', $updateContactNumber, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $updateEmail, PDO::PARAM_STR);
        $updateStmt->bindParam(':username', $updateUsername, PDO::PARAM_STR);
        $updateStmt->bindParam(':userID', $updateUserID, PDO::PARAM_INT);
        $updateStmt->execute();

        // Actualizar las variables de sesión solo si el usuario actualizado es el mismo que está autenticado
        if ($_SESSION['tbl_user_id'] == $updateUserID) {
            $_SESSION['first_name'] = $updateFirstName;
            $_SESSION['last_name'] = $updateLastName;
        }

        $conn->commit();
        echo 'Usuario actualizado exitosamente';
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Método de solicitud no permitido";
}
