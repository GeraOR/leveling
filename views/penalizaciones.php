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
    <link rel="stylesheet" href="../css/penalizaciones.css">
    <title>Penalizaciones - Solo Leveling</title>
</head>
<body>
    <header>
        <h1>Penalizaciones</h1>
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
            <h2>Historial de Penalizaciones</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Puntos Perdidos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2025-03-11</td>
                        <td>No completó tarea diaria</td>
                        <td>-10 XP</td>
                    </tr>
                    <tr>
                        <td>2025-03-10</td>
                        <td>Inactividad prolongada</td>
                        <td>-5 XP</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Reglas de Penalización</h2>
            <ul class="task-list">
                <li>No completar una tarea diaria: -10 XP</li>
                <li>Faltar una semana sin actividad: -50 XP</li>
                <li>Abandonar una tarea sin motivo: -20 XP</li>
            </ul>
        </section>
    </main>
</body>
</html>
