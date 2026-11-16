<?php
include('../admin/conn/conn.php');
session_start();

if (isset($_SESSION['user_verification_id'])) {
    $userVerificationID = $_SESSION['user_verification_id'];
} else {
    echo "
    <script>
        alert('No se encontró ningún usuario para verificar.');
        window.location.href = './index.php';
    </script>
    ";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inicio de Sesión con Verificación de Correo Electrónico</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="./php-icon.png" type="image/x-icon">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
        }

        .verification-form {
            backdrop-filter: blur(100px);
            color: black;
            padding: 40px;
            width: 500px;
            border: 2px solid;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="main">

        <!-- Área de Verificación de Correo Electrónico -->

        <div class="verification-container">

            <div class="verification-form" id="loginForm">
                <h2 class="text-center">Verificación de Correo Electrónico</h2>
                <p class="text-center">Por favor revisa tu correo electrónico para el código de verificación.</p>
                <form action="./endpoint/verify-user.php" method="POST">
                    <input type="hidden" name="user_verification_id" value="<?= htmlspecialchars($userVerificationID) ?>">
                    <input type="number" class="form-control text-center" id="verificationCode" name="verification_code" required>
                    <button type="submit" class="btn btn-secondary login-btn form-control mt-4" name="verify">Verificar</button>
                </form>
            </div>

        </div>

    </div>

    <!-- Bootstrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

</body>

</html>