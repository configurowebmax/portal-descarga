<?php
include('../admin/conn/conn.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tbl_user_id'])) {
    header("Location: index.php");
    exit();
}

// Obtener los datos del usuario de la sesión
$firstName = $_SESSION['first_name'] ?? 'Nombre';
$lastName = $_SESSION['last_name'] ?? 'Apellido';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inicio de Sesión con Verificación de Correo Electrónico</title>
    <!-- Bootstrap CSS actualizado -->
    <link rel="shortcut icon" href="./php-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
            flex-direction: column;
            align-items: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            padding: 20px;
        }

        .content {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            width: 100%;
            max-width: 960px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .table {
            color: rgb(0, 0, 0);
        }

        td button {
            font-size: 16px;
            width: auto;
            padding: 5px 10px;
        }

        .navbar {
            width: 100%;
            padding: 0.5rem 1rem;
        }

        .modal-content {
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .content {
                padding: 15px;
            }

            .table {
                font-size: 14px;
            }

            td button {
                font-size: 14px;
                padding: 4px 8px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="width: 100%;">
        <a class="navbar-brand" href="home.php">Hola, <span id="userFullName"><?php echo htmlspecialchars($firstName) . ' ' . htmlspecialchars($lastName); ?></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="./endpoint/logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h4>Lista de usuarios</h4>
        <hr>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID de Usuario</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellido</th>
                        <th scope="col">Número de Contacto</th>
                        <th scope="col">Correo Electrónico</th>
                        <th scope="col">Nombre de Usuario</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM `tbl_user`");
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                    foreach ($result as $row) {
                        $userID = $row['tbl_user_id'];
                        $firstName = $row['first_name'];
                        $lastName = $row['last_name'];
                        $contactNumber = $row['contact_number'];
                        $email = $row['email'];
                        $username = $row['username'];
                        $password = $row['password'];
                    ?>
                        <tr id="userRow-<?= $userID ?>">
                            <td id="userID-<?= $userID ?>"><?php echo $userID ?></td>
                            <td id="firstName-<?= $userID ?>"><?php echo htmlspecialchars($firstName) ?></td>
                            <td id="lastName-<?= $userID ?>"><?php echo htmlspecialchars($lastName) ?></td>
                            <td id="contactNumber-<?= $userID ?>"><?php echo htmlspecialchars($contactNumber) ?></td>
                            <td id="email-<?= $userID ?>"><?php echo htmlspecialchars($email) ?></td>
                            <td id="username-<?= $userID ?>"><?php echo htmlspecialchars($username) ?></td>
                            <td>
                                <button id="editBtn" onclick="update_user(<?php echo $userID ?>)" title="Editar">&#9998;</button>
                                <button id="deleteBtn" onclick="delete_user(<?php echo $userID ?>)">&#128465;</button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.getElementById('updateUserForm').addEventListener('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);

                fetch('./endpoint/update-user.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.text())
                    .then(result => {
                        alert(result); // Mostrar mensaje de éxito o error

                        if (result.includes('exitosamente')) {
                            let userID = document.getElementById('updateUserID').value;
                            let newFirstName = document.getElementById('updateFirstName').value;
                            let newLastName = document.getElementById('updateLastName').value;
                            document.getElementById('firstName-' + userID).textContent = newFirstName;
                            document.getElementById('lastName-' + userID).textContent = newLastName;
                            document.getElementById('contactNumber-' + userID).textContent = document.getElementById('updateContactNumber').value;
                            document.getElementById('email-' + userID).textContent = document.getElementById('updateEmail').value;
                            document.getElementById('username-' + userID).textContent = document.getElementById('updateUsername').value;

                            // Actualizar el nombre en el header
                            if (<?php echo $_SESSION['tbl_user_id']; ?> == userID) {
                                document.getElementById('userFullName').textContent = newFirstName + ' ' + newLastName;
                            }

                            const modal = bootstrap.Modal.getInstance(document.getElementById('updateUserModal'));
                            modal.hide();
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        function update_user(id) {
            const modal = new bootstrap.Modal(document.getElementById('updateUserModal'));
            modal.show();

            let updateUserID = document.getElementById("userID-" + id).textContent;
            let updateFirstName = document.getElementById("firstName-" + id).textContent;
            let updateLastName = document.getElementById("lastName-" + id).textContent;
            let updateContactNumber = document.getElementById("contactNumber-" + id).textContent;
            let updateEmail = document.getElementById("email-" + id).textContent;
            let updateUsername = document.getElementById("username-" + id).textContent;

            document.getElementById("updateUserID").value = updateUserID;
            document.getElementById("updateFirstName").value = updateFirstName;
            document.getElementById("updateLastName").value = updateLastName;
            document.getElementById("updateContactNumber").value = updateContactNumber;
            document.getElementById("updateEmail").value = updateEmail;
            document.getElementById("updateUsername").value = updateUsername;
            document.getElementById("updatePassword").value = '';
            document.getElementById("updatePassword").placeholder = 'Dejar en blanco si no desea cambiar';
        }

        function delete_user(id) {
            if (confirm("¿Deseas eliminar a este usuario?")) {
                window.location = "./endpoint/delete-user.php?user=" + id;
            }
        }
    </script>

    <!-- Modal de Actualización -->
    <div class="modal fade mt-5" id="updateUserModal" tabindex="-1" aria-labelledby="updateUser" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateUserModalLabel">Actualizar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm">
                        <div class="form-group row">
                            <div class="col-6">
                                <input type="text" name="tbl_user_id" id="updateUserID" hidden>
                                <label for="updateFirstName">Nombre:</label>
                                <input type="text" class="form-control" id="updateFirstName" name="first_name" required>
                            </div>
                            <div class="col-6">
                                <label for="updateLastName">Apellido:</label>
                                <input type="text" class="form-control" id="updateLastName" name="last_name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-5">
                                <label for="updateContactNumber">Número de Contacto:</label>
                                <input type="number" class="form-control" id="updateContactNumber" name="contact_number" maxlength="11" required>
                            </div>
                            <div class="col-7">
                                <label for="updateEmail">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="updateEmail" name="email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="updateUsername">Nombre de Usuario:</label>
                            <input type="text" class="form-control" id="updateUsername" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="updatePassword">Contraseña:</label>
                            <input type="password" class="form-control" id="updatePassword" name="password" placeholder="Dejar en blanco si no desea cambiar">
                        </div>
                        <button type="submit" class="btn btn-dark login-register form-control">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS actualizado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>