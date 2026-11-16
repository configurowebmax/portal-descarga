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
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($user['role'] == 'user') {
    header("Location: index.php");
    exit();
}

// Obtener la información del usuario a editar
$edit_user_id = intval($_GET['user_id']);

// Redirigir si el `user_id` es 1
if ($edit_user_id === 1) {
    header("Location: list_users.php");
    exit();
}

$stmt = $conn->prepare("SELECT username, role FROM tbl_users WHERE user_id = :edit_user_id");
$stmt->bindParam(':edit_user_id', $edit_user_id, PDO::PARAM_INT);
$stmt->execute();
$edit_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$edit_user) {
    header("Location: list_users.php");
    exit();
}

// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Token CSRF inválido.");
    }

    $username = filter_var(trim($_POST["username"]), FILTER_SANITIZE_STRING);
    $password = trim($_POST["password"]);
    $role = filter_var(trim($_POST["role"]), FILTER_SANITIZE_STRING);

    if (!empty($username) && ($role == 'user' || $role == 'admin')) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE tbl_users SET username = :username, password = :password, role = :role WHERE user_id = :edit_user_id");
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        } else {
            $stmt = $conn->prepare("UPDATE tbl_users SET username = :username, role = :role WHERE user_id = :edit_user_id");
        }
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':edit_user_id', $edit_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $successMessage = "Usuario actualizado exitosamente.";
        $edit_user['username'] = $username;
        $edit_user['role'] = $role;
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
    <title>Editar Usuario</title>

    <!-- Style CSS -->
    <link rel="stylesheet" href="./assets/style.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="main">
        <div class="alert alert-dark text-center" role="alert">
            <h2>Editar Usuario</h2>
            <?php include('assets/header.php') ?>
        </div>
        <div class="edit-user-container">
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
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($edit_user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Nueva Contraseña (dejar en blanco para mantener la actual):</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="user" <?php echo ($edit_user['role'] == 'user') ? 'selected' : ''; ?>>Usuario</option>
                        <option value="admin" <?php echo ($edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark form-control">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>