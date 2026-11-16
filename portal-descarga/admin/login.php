<?php
session_start();
include("./conn/conn.php");

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = trim($_POST["username"]);
    $enteredPassword = trim($_POST["password"]);

    // Sanitizar entradas
    $enteredUsername = filter_var($enteredUsername, FILTER_SANITIZE_STRING);

    // Preparar la consulta para seleccionar el hash de la contraseña
    $stmt = $conn->prepare("SELECT user_id, password FROM tbl_users WHERE username = :username");
    $stmt->bindParam(':username', $enteredUsername, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($enteredPassword, $user['password'])) {
        // Iniciar sesión y redirigir al usuario
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = "Usuario o Contraseña Incorrecta";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <?php include('assets/head.php'); ?>
</head>

<body>
    <div class="main">
        <div class="login-container">
            <h2 class="text-center">Sistema de Descarga de Productos con Registro de Usuarios</h2>
            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-dark form-control">Acceder</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>