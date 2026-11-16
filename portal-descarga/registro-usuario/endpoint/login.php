<?php
include('../../admin/conn/conn.php');
session_start(); // Iniciar la sesión para almacenar datos del usuario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM `tbl_customer` WHERE `username` = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $stored_password = $row['password'];

        if (password_verify($password, $stored_password)) {
            // Almacenar los datos del usuario en variables de sesión
            $_SESSION['tbl_customer_id'] = $row['tbl_customer_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];

            echo "
            <script>
                alert('¡Inicio de sesión exitoso!');
                window.location.href = '../../index.php';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('¡Inicio de sesión fallido, contraseña incorrecta!');
                window.location.href = '../index.php';
            </script>
            ";
        }
    } else {
        echo "
            <script>
                alert('¡Inicio de sesión fallido, usuario no encontrado!');
                window.location.href = '../index.php';
            </script>
        ";
    }
}
