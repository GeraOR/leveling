<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}
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
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Información Personal</h2>
            <p><strong>Nombre:</strong> [Nombre del Usuario]</p>
            <p><strong>Correo Electrónico:</strong> [correo@example.com]</p>
        </section>

        <section>
            <h2>Progreso</h2>
            <div class="progreso">
                <p><strong>Nivel:</strong> <span>1</span></p>
                <p><strong>Experiencia:</strong> <span>0/100</span></p>
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
            <form action="update_profile.php" method="POST">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" value="[Nombre del Usuario]" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="[correo@example.com]" required>

                <button type="submit">Guardar Cambios</button>
            </form>
        </section>
    </main>
</body>
</html>
