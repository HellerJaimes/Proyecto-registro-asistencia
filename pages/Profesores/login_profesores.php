<!DOCTYPE html>
<?php
    // Capturar los errores de la URL
    $error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro e Inicio de Sesión de PROFESORES</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
        </div>
    </nav>

    <br><br>
    <h1 class="text-center">Bienvenido Usuario</h1>
    <br><br><br>
    <div class="d-grid gap-4 col-4 mx-auto">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#iniciosesionmodal" style="--bs-btn-padding-y: 2rem; --bs-btn-padding-x: 1rem; --bs-btn-font-size: 2rem;">Inicio de sesión</button>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#registromodal" style="--bs-btn-padding-y: 2rem; --bs-btn-padding-x: 1rem; --bs-btn-font-size: 2rem;">Registrarse</button>
    </div>

    <!-- Modal de Inicio de Sesión -->
    <div class="modal fade" id="iniciosesionmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ingrese su cuenta</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Mostrar mensajes de error -->
                    <?php if ($error == 'incorrect_password'): ?>
                        <div class="alert alert-danger" role="alert">
                            Contraseña incorrecta. Intente nuevamente.
                        </div>
                    <?php elseif ($error == 'user_not_found'): ?>
                        <div class="alert alert-danger" role="alert">
                            Usuario no encontrado.
                        </div>
                    <?php endif; ?>

                    <form id="iniciosesion-form" action="login.php" method="POST">
                        <label for="username-login">Nombre de usuario:</label>
                        <input type="text" class="form-control mb-3" placeholder="Username" name="username-login" id="username-login" required>
                        <br>
                        <label for="password-login">Contraseña:</label>
                        <input type="password" class="form-control mb-3" placeholder="Contraseña" name="password-login" id="password-login" required>
                        <br>
                        <button type="submit" class="d-grid gap-3 col-4 mx-auto btn btn-primary btn-lg">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Registro Modal -->
    <div class="modal fade" id="registromodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Crea una nueva cuenta</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registro-form" action="registro.php" method="POST">
                        <label for="registro-username">Nombre de usuario:</label>
                        <input type="text" class="form-control mb-3" placeholder="Username" name="registro-username" id="registro-username" required>
                        <br>
                        <label for="registro-password">Contraseña:</label>
                        <input type="password" class="form-control mb-3" placeholder="Contraseña" name="registro-password" id="registro-password" required>
                        <br>
                        <button type="submit" class="d-grid gap-3 col-4 mx-auto btn btn-primary btn-lg">Registro</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
