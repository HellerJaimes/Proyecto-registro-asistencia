<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencias</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Bootstrap JS and Toastify -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sistema de Registro de Asistencias</span>
            <div class="d-flex">
                <button class="btn btn-danger" type="button" onclick="location.href='/Superadmin/superadmin.php';">
                    Superadmin
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Título -->
    <br><br>
    <h1 class="text-center">Bienvenido</h1>
    <br><br><br>

    <!-- Botones de Login -->
    <div class="d-grid gap-4 col-4 mx-auto">
    <div class="d-flex justify-content-center gap-4">
    <button class="btn btn-primary" type="button" onclick="location.href='/pages/Estudiantes/login_estudiantes.php';" style="--bs-btn-padding-y: 2rem; --bs-btn-padding-x: 1rem; --bs-btn-font-size: 2rem;">
        Login Estudiantes
    </button>

    <button class="btn btn-primary" type="button" onclick="location.href='/pages/Profesores/login_profesores.php';" style="--bs-btn-padding-y: 2rem; --bs-btn-padding-x: 1rem; --bs-btn-font-size: 2rem;">
        Login Profesores
    </button>
</div>

</body>
</html>
