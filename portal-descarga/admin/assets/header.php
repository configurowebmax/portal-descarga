<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('./conn/conn.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} else {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, role FROM tbl_users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];
    $role = $user['role'];
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand ml-3" href="">Hola, <?php echo htmlspecialchars($username); ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Archivos</a>
            </li>
            <?php if ($role == 'admin') : ?>
                <li class="nav-item">
                    <a class="nav-link" href="register_user.php">Crear Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="list_users.php">Listar Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customers.php">Clientes Registrados</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="endpoint/logout.php">Cerrar Sesión</a>
            </li>
        </ul>
    </div>
</nav>