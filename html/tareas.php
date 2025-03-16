<?php
include "../includes/db.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/tareas.css">
    <title>Mis Tareas - Solo Leveling</title>
</head>
<body>
    <header>
        <h1>Mis Tareas</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Lista de Tareas</h2>
            <ul class="task-list">
                <li>
                    <input type="checkbox" id="task1">
                    <label for="task1">Ejemplo de tarea 1</label>
                </li>
                <li>
                    <input type="checkbox" id="task2">
                    <label for="task2">Ejemplo de tarea 2</label>
                </li>
            </ul>
        </section>

        <section>
            <h2>Agregar Nueva Tarea</h2>
            <form action="add_task.php" method="POST">
                <label for="task">Tarea:</label>
                <input type="text" id="task" name="task" required>

                <label for="due_date">Fecha límite:</label>
                <input type="date" id="due_date" name="due_date">

                <button type="submit">Agregar</button>
            </form>
        </section>
    </main>
</body>
</html>
