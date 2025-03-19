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
    <link rel="stylesheet" href="../css/motivacion.css">
    <title>Motivación - Solo Leveling</title>
</head>
<body>
    <header>
        <h1>Motivación y Recompensas</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="tareas.php">Mis Tareas</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Logros Desbloqueados</h2>
            <ul class="task-list">
                <li>¡Nivel 1 alcanzado! - 10 XP</li>
                <li>Primera tarea completada - 5 XP</li>
                <li>Semana sin penalizaciones - 15 XP</li>
            </ul>
        </section>

        <section>
            <h2>Frases Motivadoras</h2>
            <p>"No importa cuántas veces caigas, lo que importa es cuántas veces te levantas."</p>
            <p>"El progreso es la clave, no la perfección."</p>
            <p>"Hoy es un buen día para comenzar de nuevo."</p>
        </section>

        <section>
            <h2>Recompensas</h2>
            <p>¡Has alcanzado 100 XP! Recibes una nueva habilidad.</p>
            <p>¡Has completado 5 tareas seguidas! Obtienes una medalla de logro.</p>
        </section>
    </main>
</body>
</html>
