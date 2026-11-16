<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../../admin/conn/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

if (isset($_POST['register'])) {
    try {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $contactNumber = $_POST['contact_number'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la contraseña

        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT `first_name`, `last_name` FROM `tbl_customer` WHERE `first_name` = :first_name AND `last_name` = :last_name");
        $stmt->execute([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);
        $nameExist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($nameExist)) {
            $verificationCode = rand(100000, 999999);

            $insertStmt = $conn->prepare("INSERT INTO `tbl_customer` (`first_name`, `last_name`, `contact_number`, `email`, `username`, `password`, `verification_code`) VALUES (:first_name, :last_name, :contact_number, :email, :username, :password, :verification_code)");
            $insertStmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
            $insertStmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
            $insertStmt->bindParam(':contact_number', $contactNumber, PDO::PARAM_STR);
            $insertStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $insertStmt->bindParam(':username', $username, PDO::PARAM_STR);
            $insertStmt->bindParam(':password', $password, PDO::PARAM_STR);
            $insertStmt->bindParam(':verification_code', $verificationCode, PDO::PARAM_INT);
            $insertStmt->execute();

            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pruebas@demoscweb.com';
            $mail->Password   = 'oK7!cU7*nW6$kT9$iJ3*';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cambiado de 'ssl' a 'tls'
            $mail->Port       = 587; // Puerto correcto para TLS
            $mail->SMTPDebug = 0; // Cambiar a 0 para deshabilitar la depuración detallada
            $mail->CharSet = 'UTF-8'; // Establecer la codificación a UTF-8

            // Destinatarios
            $mail->setFrom('pruebas@demoscweb.com', 'ConfiguroWeb');
            $mail->addAddress($email);
            $mail->addReplyTo('pruebas@demoscweb.com', 'ConfiguroWeb');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Código de Verificación';
            $mail->Body    = 'Tu código de verificación es: ' . $verificationCode;

            // Alerta de mensaje enviado con éxito
            $mail->send();

            session_start();

            $userVerificationID = $conn->lastInsertId();
            $_SESSION['user_verification_id'] = $userVerificationID;
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;

            echo "
            <script>
                alert('Verifica en tu correo electrónico tu código de verificación');
                window.location.href = '../verification.php';
            </script>
            ";

            $conn->commit();
        } else {
            echo "
            <script>
                alert('El usuario ya existe');
                window.location.href = '../index.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
