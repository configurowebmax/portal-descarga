<?php
session_start();  // Iniciar sesión

include('./admin/conn/conn.php');

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['tbl_customer_id']);

// Verificar si se ha enviado un término de búsqueda
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
}

// Obtener la lista de productos aprobados
$sql = "
    SELECT f.file_title, f.file, f.file_uploader, p.description, p.image, p.price, f.tbl_file_id 
    FROM tbl_file f 
    LEFT JOIN tbl_product p ON f.tbl_file_id = p.file_id 
    WHERE f.status = 'approved' AND p.product_id IS NOT NULL
";

if ($searchTerm != '') {
    $sql .= " AND (f.file_title LIKE :searchTerm OR f.file_uploader LIKE :searchTerm OR p.description LIKE :searchTerm)";
    $stmt = $conn->prepare($sql);
    $searchTermParam = '%' . $searchTerm . '%';
    $stmt->bindParam(':searchTerm', $searchTermParam, PDO::PARAM_STR);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('./assets/header.php'); ?>
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .card-text {
            flex: 1;
        }

        .btn-block {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column min-vh-100">
        <div class="container mt-5 pt-5 flex-grow-1">
            <div class="text-center">
                <h1 class="display-4">Descarga de Productos</h1>
                <p class="lead">Regístrate y accede a tus productos fácilmente</p>
            </div>

            <!-- Formulario de búsqueda -->
            <form class="form-inline my-4 justify-content-center" method="GET" action="">
                <input class="form-control form-control-lg mr-sm-2" type="search" placeholder="Buscar productos" aria-label="Buscar" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-success btn-lg my-2 my-sm-0" type="submit">Buscar</button>
            </form>

            <div class="row">
                <?php foreach ($files as $file) : ?>
                    <div class="col-lg-4 col-md-6 d-flex">
                        <div class="card mb-4 shadow-sm w-100">
                            <?php if (!empty($file['image'])) : ?>
                                <img src="./admin/product-images/<?php echo htmlspecialchars($file['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($file['file_title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($file['file_title']); ?></h5>
                                <?php if (!empty($file['description'])) : ?>
                                    <p class="card-text"><?php echo htmlspecialchars($file['description']); ?></p>
                                    <p class="card-text">$<?php echo number_format($file['price'], 2); ?></p>
                                <?php endif; ?>
                                <?php if ($isAuthenticated) : ?>
                                    <a href="./download.php?fileID=<?php echo htmlspecialchars($file['tbl_file_id']); ?>" class="btn btn-primary btn-block">Descargar</a>
                                <?php else : ?>
                                    <a href="./registro-usuario/index.php" class="btn btn-primary btn-block">Descargar</a>
                                <?php endif; ?>
                                <p class="card-text mt-2 text-muted">Subido por: <?php echo htmlspecialchars($file['file_uploader']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($files)) : ?>
                    <div class="col-12">
                        <p class="text-center">No se encontraron productos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php include('./assets/footer.php'); ?>
    </div>
</body>

</html>