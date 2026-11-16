<?php
session_start();
include('../../admin/conn/conn.php');

if (isset($_POST['verify'])) {
    $userVerificationID = $_POST['user_verification_id'];
    $verificationCode = $_POST['verification_code'];

    $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE tbl_customer_id = :userVerificationID AND verification_code = :verificationCode");
    $stmt->bindParam(':userVerificationID', $userVerificationID, PDO::PARAM_INT);
    $stmt->bindParam(':verificationCode', $verificationCode, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Marcar al usuario como verificado eliminando el código de verificación
        $updateStmt = $conn->prepare("UPDATE tbl_customer SET verification_code = NULL WHERE tbl_customer_id = :userVerificationID");
        $updateStmt->bindParam(':userVerificationID', $userVerificationID, PDO::PARAM_INT);
        $updateStmt->execute();

        // Iniciar sesión del usuario
        $_SESSION['tbl_customer_id'] = $user['tbl_customer_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];

        echo "
        <script>
            alert('Verificación exitosa. ¡Bienvenido!');
            window.location.href = '../../index.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Código de verificación incorrecto.');
            window.location.href = '../verification.php';
        </script>
        ";
    }
} else {
    echo "
    <script>
        alert('Solicitud no válida.');
        window.location.href = '../index.php';
    </script>
    ";
}
