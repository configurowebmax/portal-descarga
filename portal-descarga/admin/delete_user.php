<?php
session_start();
include('./conn/conn.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
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
    header("Location: ../login.php");
    exit();
}

// Verificar el rol del usuario y redirigir si es "user"
if ($user['role'] == 'user') {
    header("Location: ../index.php");
    exit();
}

// Obtener el user_id del usuario a eliminar y validarlo
if (!isset($_GET['user_id']) || !filter_var($_GET['user_id'], FILTER_VALIDATE_INT)) {
    header("Location: ../list_users.php");
    exit();
}

$delete_user_id = intval($_GET['user_id']);

// Prevenir la eliminación del usuario "configuroweb"
$stmt = $conn->prepare("SELECT username FROM tbl_users WHERE user_id = :delete_user_id");
$stmt->bindParam(':delete_user_id', $delete_user_id, PDO::PARAM_INT);
$stmt->execute();
$userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userToDelete && $userToDelete['username'] === 'configuroweb') {
    header("Location: ../list_users.php");
    exit();
}

// Eliminar el usuario
$stmt = $conn->prepare("DELETE FROM tbl_users WHERE user_id = :delete_user_id");
$stmt->bindParam(':delete_user_id', $delete_user_id, PDO::PARAM_INT);
$stmt->execute();

header("Location: ./list_users.php");
exit();
