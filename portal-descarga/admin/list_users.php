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

$stmt = $conn->prepare("SELECT user_id, username, role FROM tbl_users WHERE username != 'configuroweb'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>

    <!-- Style CSS -->
    <link rel="stylesheet" href="./assets/style.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />


    <link rel="icon" type="image/x-icon" href="assets/img/php-icon.png">
</head>

<body>
    <div class="main">
        <div class="alert alert-dark text-center" role="alert">
            <h2>Lista de Usuarios</h2>
            <?php include('assets/header.php') ?>
        </div>
        <div class="user-list-container">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td>
                                <a href="edit_user.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-secondary btn-sm" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                <a href="delete_user.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este usuario?');"><i class="fa-solid fa-times"></i></a>
                            </td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>