<?php
session_start();
include('./conn/conn.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener la información del usuario logueado
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM tbl_users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Si no se encuentra el usuario, destruir la sesión y redirigir al login
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar el rol del usuario y redirigir si es "user"
if ($user['role'] == 'user') {
    header("Location: index.php");
    exit();
}

// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar el token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Token CSRF inválido.");
    }

    // Sanitizar entradas
    $username = filter_var(trim($_POST["username"]), FILTER_SANITIZE_STRING);
    $password = trim($_POST["password"]);
    $role = filter_var(trim($_POST["role"]), FILTER_SANITIZE_STRING);

    // Validar entradas
    if (!empty($username) && !empty($password) && !empty($role) && ($role == 'user' || $role == 'admin')) {
        // Verificar si el nombre de usuario ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Nombre de usuario no existe, proceder con la inserción
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO tbl_users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();
            $successMessage = "Usuario creado exitosamente.";
        } else {
            $errorMessage = "El nombre de usuario ya existe.";
        }
    } else {
        $errorMessage = "Por favor, complete todos los campos correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuarios</title>

    <!-- Style CSS -->
    <link rel="stylesheet" href="./assets/style.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="assets/img/php-icon.png">
</head>

<body>
    <div class="main">
        <div class="alert alert-dark text-center" role="alert">
            <h2>Crear Usuarios</h2>
            <?php include('assets/header.php') ?>
        </div>
        <div class="register-user-container">
            <?php if (isset($successMessage)) : ?>
                <div class="alert alert-success text-center" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <form action="#" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark form-control">Crear Usuario</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>