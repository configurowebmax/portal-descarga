<?php
include('./admin/conn/conn.php');

if (isset($_GET['user'])) {
    $userID = $_GET['user'];

    try {
        $query = "DELETE FROM `tbl_user` WHERE `tbl_user_id` = :userID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $query_execute = $stmt->execute();

        if ($query_execute) {
            echo "
            <script>
                alert('Usuario eliminado exitosamente');
                window.location.href = '../home.php';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Error al eliminar el usuario');
                window.location.href = '../home.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
