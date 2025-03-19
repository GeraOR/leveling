<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener datos del usuario
$sql = "SELECT nombre, email, nivel, xp FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <title>Perfil - Solo Leveling</title>
</head>
<body>
    <header>
        <h1>Perfil de Usuario</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="tareas.php">Mis Tareas</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Información Personal</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario["nombre"]); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($usuario["email"]); ?></p>
        </section>

        <section>
            <h2>Progreso</h2>
            <div class="progreso">
                <p><strong>Nivel:</strong> <span><?php echo $usuario["nivel"]; ?></span></p>
                <p><strong>Experiencia:</strong> <span><?php echo $usuario["xp"]; ?>/100</span></p>
                <p><strong>Rango:</strong> <span>Novato</span></p>
            </div>
        </section>

        <section>
            <h2>Gestión Personal</h2>
            <a href="motivacion.php" class="task-link">Ver Motivaciones</a>
            <a href="penalizaciones.php" class="task-link">Ver Penalizaciones</a>
        </section>
        
        <section>
            <h2>Editar Perfil</h2>
            <form action="../scripts/update_profile.php" method="POST">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario["email"]); ?>" required>
                
                <button type="submit">Guardar Cambios</button>
            </form>
        </section>

        <section>
            <h2>Cambiar Contraseña</h2>
            <form action="update_password.php" method="POST">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Cambiar Contraseña</button>
            </form>
        </section>
    </main>
</body>
</html>
