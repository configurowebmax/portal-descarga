<?php include('../admin/conn/conn.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inicio de Sesión con Verificación de Correo Electrónico</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="./php-icon.png" type="image/x-icon">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .login-form,
        .registration-form {
            backdrop-filter: blur(20px);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            width: 400px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .switch-form-link {
            text-decoration: none;
            cursor: pointer;
            color: rgb(100, 100, 200);
        }
    </style>
</head>

<body>

    <div class="main">

        <!-- Área de Inicio de Sesión -->
        <div class="login-container">

            <div class="login-form" id="loginForm">
                <h2 class="text-center">¡Accede para Comprar!</h2>
                <p class="text-center">Solo toma unos segundos el proceso</p>
                <form action="./endpoint/login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Nombre de Usuario:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <p>¿No tienes una cuenta? Regístrate <span class="switch-form-link" onclick="showRegistrationForm()">Aquí.</span></p>
                    <button type="submit" class="btn btn-primary form-control">Iniciar Sesión</button>
                </form>
            </div>

        </div>

        <!-- Área de Registro -->
        <div class="registration-form" id="registrationForm">
            <h2 class="text-center">Formulario de Registro</h2>
            <p class="text-center">Completa tus datos personales.</p>
            <form action="./endpoint/add-user.php" method="POST">
                <div class="form-group registration row">
                    <div class="col-6">
                        <label for="firstName">Nombre:</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    <div class="col-6">
                        <label for="lastName">Apellido:</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                </div>
                <div class="form-group registration row">
                    <div class="col-5">
                        <label for="contactNumber">Número de Contacto:</label>
                        <input type="number" class="form-control" id="contactNumber" name="contact_number" maxlength="11" required>
                    </div>
                    <div class="col-7">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group registration">
                    <label for="registerUsername">Nombre de Usuario:</label>
                    <input type="text" class="form-control" id="registerUsername" name="username" required>
                </div>
                <div class="form-group registration">
                    <label for="registerPassword">Contraseña:</label>
                    <input type="password" class="form-control" id="registerPassword" name="password" required>
                </div>
                <p>¿Ya tienes una cuenta? Inicia Sesión <span class="switch-form-link" onclick="showLoginForm()">Aquí.</span></p>
                <button type="submit" class="btn btn-dark form-control" name="register">Registrar</button>
            </form>
        </div>

    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registrationForm = document.getElementById('registrationForm');

        registrationForm.style.display = "none";

        function showRegistrationForm() {
            registrationForm.style.display = "block";
            loginForm.style.display = "none";
        }

        function showLoginForm() {
            registrationForm.style.display = "none";
            loginForm.style.display = "block";
        }
    </script>

    <!-- Bootstrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@2.10.2/dist/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>

</html>