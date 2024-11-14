<?php
session_start();

// Verificar si el superadmin está autenticado
if (!isset($_SESSION['superadmin_logged_in'])) {
    header("Location: superadmin.php"); // Redirigir si no está autenticado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['registro']; // Obtener la opción seleccionada

    // Redirigir a la página correspondiente
    if ($type === 'estudiante') {
        header("Location: superadmin_dashboardEstudiantes.php");
    } elseif ($type === 'profesor') {
        header("Location: superadmin_dashboardProfesores.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Registro</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Estilos Personalizados -->
    <style>
        body {
            background-color: #2a2a2a; /* Fondo oscuro sobrio */
            color: #ffffff; /* Texto en blanco */
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 500px;
            background-color: #333333; /* Fondo del formulario en gris oscuro */
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7); /* Sombra para dar profundidad */
        }
        h1 {
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #e9ecef; /* Tono gris claro */
        }
        .form-group label {
            font-weight: 500;
            color: #b8b8b8; /* Texto de etiquetas en gris claro */
        }
        .form-control {
            background-color: #444444; /* Fondo de los campos de entrada */
            color: #ffffff;
            border: 1px solid #555555; /* Borde gris oscuro */
            border-radius: 5px;
        }
        .form-control:focus {
            background-color: #555555;
            color: #ffffff;
            border-color: #1a73e8; /* Borde de enfoque en azul */
            box-shadow: 0 0 5px rgba(26, 115, 232, 0.5); /* Sombra de enfoque */
        }
        .btn-primary {
            background-color: #1a73e8; /* Azul fuerte para el botón */
            border: none;
            padding: 0.6rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0f5bb5; /* Azul más oscuro al pasar el ratón */
        }
        .text-center {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Registro de Estudiante o Profesor</h1>
        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="registro">Elija el tipo de registro que desea realizar:</label>
                <select class="form-control" id="registro" name="registro">
                    <option value="estudiante">Registrar Estudiante</option>
                    <option value="profesor">Registrar Profesor</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Continuar</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
