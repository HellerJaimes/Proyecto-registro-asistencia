<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Estudiantes</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Estilos Personalizados -->
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 1rem;
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #ffffff;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
        }
        .form-label {
            font-weight: 500;
            color: #b8daff;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: inset 0 0 5px rgba(255, 255, 255, 0.1);
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #198754;
            border: none;
            padding: 0.75rem;
            font-size: 1.2rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #145a32;
        }
        .alert {
            text-align: center;
            color: #fff;
            background-color: rgba(255, 0, 0, 0.8);
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Inicio de Sesión</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                if ($_GET['error'] === 'invalid_credentials') {
                    echo "Username o contraseña incorrecta. Inténtalo de nuevo.";
                } elseif ($_GET['error'] === 'user_not_found') {
                    echo "El usuario no fue encontrado. Verifica tu username.";
                } elseif ($_GET['error'] === 'incorrect_password') {
                    echo "Contraseña incorrecta. Inténtalo de nuevo.";
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="logic_login_estudiantes.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
