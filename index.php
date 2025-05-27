<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencias</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
    <!-- Estilos Personalizados -->
    <style>
        body {
            background-color: #2c2f33;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .navbar {
            background-color: #23272a;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff;
        }

        .hero-section {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            padding: 5rem 1rem;
            border-radius: 1rem;
            color: #ffffff;
            text-align: center;
            margin: 2rem 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .hero-section p {
            font-size: 1.5rem;
            font-weight: 300;
        }

        .btn-custom {
            padding: 1.5rem 2rem;
            font-size: 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background-color: #5865f2;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #4752c4;
            transform: translateY(-4px);
        }

        .btn-danger-custom {
            background-color: #f04747;
            border: none;
        }

        .btn-danger-custom:hover {
            background-color: #c23030;
            transform: translateY(-4px);
        }

        footer {
            text-align: center;
            margin-top: 3rem;
            padding: 1rem 0;
            background-color: #23272a;
            color: #99aab5;
        }
    </style>
</head>
<body>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Registro de Asistencias</a>
            <button class="btn btn-danger btn-danger-custom" type="button" onclick="location.href='/Superadmin/superadmin.php';">
                Superadmin
            </button>
        </div>
    </nav>
    
    <!-- Sección Hero Mejorada -->
    <div class="hero-section">
        <h1 class="display-4">¡Bienvenido!</h1>
        <p class="lead">Gestiona y registra asistencias de forma rápida y sencilla.</p>
    </div>

    <!-- Botones de Login -->
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-md-6">
                <button class="btn btn-primary btn-custom btn-primary-custom w-100" type="button" onclick="location.href='/pages/Estudiantes/login_estudiantes.php';">
                    Login Estudiantes
                </button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary btn-custom btn-primary-custom w-100" type="button" onclick="location.href='/pages/Profesores/login_profesores.php';">
                    Login Profesores
                </button>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <footer>
        <div class="container">
            <p>Sistema de Registro de Asistencias. Heller Andres Jaimes Gelvez</p>
        </div>
    </footer>
</body>
</html>
