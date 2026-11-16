<?php
include('../conn/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $contactNumber = $_POST['contact_number'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $verificationCode = rand(100000, 999999);

    $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO tbl_user (first_name, last_name, contact_number, email, username, password, verification_code) VALUES (:first_name, :last_name, :contact_number, :email, :username, :password, :verification_code)");
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':verification_code', $verificationCode);
        $stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pruebas@demoscweb.com';
            $mail->Password = 'jW1#qB9#jJ3*xS4>kC5*';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('pruebas@demoscweb.com', 'ConfiguroWeb');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Código de Verificación';
            $mail->Body = 'Tu código de verificación es: ' . $verificationCode;

            $mail->send();
            echo "Por favor, revisa tu correo para el código de verificación.";
        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "El correo electrónico ya está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('../../assets/header.php'); ?>
</head>

<body>
    <div class="container">
        <h2>Registro</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="first_name">Nombre:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Apellido:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Número de Contacto:</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
</body>

</html>