<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <title>Dashboard - Solo Leveling</title>
</head>
<body>
    <header>
        <h1>Bienvenido a Solo Leveling</h1>
        <nav>
            <ul>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="tareas.php">Mis Tareas</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Progreso</h2>
            <div class="progreso">
                <p><strong>Nivel:</strong> <span>1</span></p>
                <p><strong>Experiencia:</strong> <span>0/100</span></p>
                <p><strong>Rango:</strong> <span>Novato</span></p>
            </div>
        </section>
        <section>
            <h2>Tareas Pendientes</h2>
            <ul class="task-list">
                <li>Ejemplo de tarea 1</li>
                <li>Ejemplo de tarea 2</li>
            </ul>
            <a href="tareas.php" class="task-link">Ver todas las tareas</a>
        </section>
    </main>
</body>
</html>
