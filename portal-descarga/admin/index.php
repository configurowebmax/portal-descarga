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

$username = $user['username'];
$role = $user['role'];

// Construir la consulta SQL según el rol del usuario
if ($role == 'admin') {
    $stmt = $conn->prepare("
        SELECT f.*, p.product_id 
        FROM tbl_file f 
        LEFT JOIN tbl_product p ON f.tbl_file_id = p.file_id
    ");
} else {
    $stmt = $conn->prepare("
        SELECT f.*, p.product_id 
        FROM tbl_file f 
        LEFT JOIN tbl_product p ON f.tbl_file_id = p.file_id
        WHERE f.file_uploader = :username
    ");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener archivos pendientes de aprobación si el usuario es administrador
if ($role == 'admin') {
    $stmt_pending = $conn->prepare("
        SELECT f.*, p.product_id 
        FROM tbl_file f 
        LEFT JOIN tbl_product p ON f.tbl_file_id = p.file_id
        WHERE f.status = 'pending' AND f.pre_published = TRUE
    ");
    $stmt_pending->execute();
    $pending_files = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
}

// Prepublicar archivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pre_publish'])) {
    $fileID = $_POST['fileID'];
    $stmt_pre_publish = $conn->prepare("UPDATE tbl_file SET pre_published = TRUE WHERE tbl_file_id = :fileID");
    $stmt_pre_publish->bindParam(':fileID', $fileID, PDO::PARAM_INT);
    $stmt_pre_publish->execute();
    header("Location: index.php");
}

// Aprobar archivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $fileID = $_POST['fileID'];
    $stmt_approve = $conn->prepare("UPDATE tbl_file SET status = 'approved' WHERE tbl_file_id = :fileID");
    $stmt_approve->bindParam(':fileID', $fileID, PDO::PARAM_INT);
    $stmt_approve->execute();
    header("Location: index.php");
}

// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Archivos</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="icon" type="image/x-icon" href="assets/img/php-icon.png">
</head>

<body>
    <div class="main">
        <div class="alert alert-dark text-center" role="alert">
            <h2>Gestión de Archivos</h2>
            <?php include('assets/header.php'); ?>
        </div>
        <div class="file-container">
            <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#addFileModal">
                <i class="fa-solid fa-plus"></i> Añadir Archivo
            </button>
            <h3>Tus Archivos</h3>
            <table class="table table-hover text-center" id="fileTable">
                <thead>
                    <tr>
                        <th scope="col">ID Archivo</th>
                        <th scope="col">Título del Archivo</th>
                        <th scope="col">Archivo</th>
                        <th scope="col">Subido Por</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $row) {
                        $fileID = htmlspecialchars($row['tbl_file_id']);
                        $fileTitle = htmlspecialchars($row['file_title']);
                        $fileName = htmlspecialchars($row['file']);
                        $fileUploader = htmlspecialchars($row['file_uploader']);
                        $dateUploaded = htmlspecialchars($row['date_uploaded']);
                    ?>
                        <tr class="fileList">
                            <th id="fileID-<?= $fileID ?>"><?php echo $fileID ?></th>
                            <td id="fileTitle-<?= $fileID ?>"><?php echo $fileTitle ?></td>
                            <td id="file-<?= $fileID ?>"><?php echo $fileName ?></td>
                            <td id="fileUploader-<?= $fileID ?>"><?php echo $fileUploader ?></td>
                            <td id="dateUploaded-<?= $fileID ?>"><?php echo $dateUploaded ?></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        Acción
                                    </button>
                                    <div class="dropdown-menu dropdown-sm text-center">
                                        <button type="button" class="btn btn-success" onclick="window.location.href='./endpoint/download-file.php?fileID=<?php echo $fileID; ?>'" title="Descargar"><i class="fa-solid fa-download"></i></button>
                                        <button type="button" class="btn btn-secondary" onclick="updateFile(<?php echo $fileID ?>)" title="Actualizar"><i class="fa-solid fa-pencil"></i></button>
                                        <form method="GET" action="./endpoint/delete-file.php">
                                            <input type="hidden" name="file" value="<?php echo $fileID; ?>">
                                            <button type="submit" class="btn btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                        <?php if ($row['status'] == 'pending' && $row['product_id'] != null) : ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="fileID" value="<?php echo $fileID; ?>">
                                                <button type="submit" name="pre_publish" class="btn btn-info">Pre Publicar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($role == 'admin' && !empty($pending_files)) : ?>
            <div class="file-container">
                <h3>Archivos Pendientes de Aprobación</h3>
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID Archivo</th>
                            <th>Título del Archivo</th>
                            <th>Archivo</th>
                            <th>Subido Por</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_files as $file) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['tbl_file_id']); ?></td>
                                <td><?php echo htmlspecialchars($file['file_title']); ?></td>
                                <td><?php echo htmlspecialchars($file['file']); ?></td>
                                <td><?php echo htmlspecialchars($file['file_uploader']); ?></td>
                                <td><?php echo htmlspecialchars($file['date_uploaded']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="fileID" value="<?php echo $file['tbl_file_id']; ?>">
                                        <button type="submit" name="approve" class="btn btn-success">Publicar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para Añadir Archivo -->
    <div class="modal fade" id="addFileModal" tabindex="-1" aria-labelledby="addFile" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFile">Añadir Archivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/add-file.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="form-group">
                            <label for="fileTitle">Título del Archivo</label>
                            <input type="text" class="form-control" id="fileTitle" name="fileTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="file">Archivo</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                        </div>
                        <div class="form-group">
                            <label for="fileUploader">Subido Por</label>
                            <input type="text" class="form-control" id="fileUploader" name="fileUploader" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        </div>
                        <!-- Campos para producto -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isProduct" name="isProduct">
                            <label class="form-check-label" for="isProduct">¿Es un producto?</label>
                        </div>
                        <div id="productFields" style="display:none;">
                            <div class="form-group">
                                <label for="productDescription">Descripción del Producto</label>
                                <textarea class="form-control" id="productDescription" name="productDescription"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="productImage">Imagen del Producto</label>
                                <input type="file" class="form-control-file" id="productImage" name="productImage">
                            </div>
                            <div class="form-group">
                                <label for="productPrice">Precio</label>
                                <input type="number" class="form-control" id="productPrice" name="productPrice" step="0.01">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" name="add_file" class="btn btn-dark">Añadir Archivo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Actualizar Archivo -->
    <div class="modal fade" id="updateFileModal" tabindex="-1" aria-labelledby="updateFile" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFile">Actualizar Archivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/update-file.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" class="form-control" id="updateFileID" name="fileID">
                        <div class="form-group">
                            <label for="updateFileTitle">Título del Archivo</label>
                            <input type="text" class="form-control" id="updateFileTitle" name="fileTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="updateFile">Archivo</label>
                            <input type="file" class="form-control-file" id="updateFile" name="file">
                        </div>
                        <div class="form-group">
                            <label for="updateFileUploader">Subido Por</label>
                            <input type="text" class="form-control" id="updateFileUploader" name="fileUploader" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-dark">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <!-- Data Tables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- Script para mostrar/ocultar campos de producto -->
    <script>
        $(document).ready(function() {
            $('#isProduct').change(function() {
                if ($(this).is(':checked')) {
                    $('#productFields').show();
                } else {
                    $('#productFields').hide();
                }
            });
        });

        function updateFile(fileID) {
            // Obtener los datos del archivo desde el DOM
            var fileTitle = document.getElementById('fileTitle-' + fileID).textContent;
            var fileUploader = document.getElementById('fileUploader-' + fileID).textContent;

            // Rellenar los campos del modal con los datos del archivo
            document.getElementById('updateFileID').value = fileID;
            document.getElementById('updateFileTitle').value = fileTitle;
            document.getElementById('updateFileUploader').value = fileUploader;

            // Mostrar el modal
            $('#updateFileModal').modal('show');
        }
    </script>
</body>

</html>