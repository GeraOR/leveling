<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
$usuario_id = $_SESSION["usuario_id"];
// Obtener tareas pendientes del usuario
$sql_tareas = "SELECT id, descripcion FROM tareas WHERE usuario_id = ? AND estado = 1 ORDER BY id ASC LIMIT 5";
$stmt = $conn->prepare($sql_tareas);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_tareas = $stmt->get_result();
$tareas = $result_tareas->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/tareas.css">
    <title>Mis Tareas - Solo Leveling</title>
    <style>
        .task-mark{
            background-color: #4CAF50;
            border: none;
            color: white;
                padding: 4px 8px;
                margin-top: auto;
                font-size: 14px;
                margin-left: 10px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.2s ease;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                -ms-border-radius: 5px;
                -o-border-radius: 5px;
}
.fade-out {
    opacity: 1;
    transition: opacity 1s ease-out;
}

.fade-out.hidden {
    opacity: 0;
}
    </style>
</head>
<body>
    <header>
        <h1>Mis Tareas</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Lista de Tareas</h2>
            <?php if (isset($_SESSION["tarea_success"])) : ?>
    <p style="color: green; font-weight: bold;"><?php echo $_SESSION["tarea_success"]; ?></p>
    <?php unset($_SESSION["tarea_success"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["tarea_error"])) : ?>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION["tarea_error"]; ?></p>
    <?php unset($_SESSION["tarea_error"]); ?>
<?php endif; ?>

            <ul class="task-list">
    <?php if (count($tareas) > 0): ?>
        <?php foreach ($tareas as $tarea): ?>
            <li>
                <form action="../scripts/marcar_completada.php" method="POST" style="display:inline;">
                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                    <button type="submit" class="task-mark"
            title="Marcar como hecha">✔</button>
                </form>
                <span style="padding-left: 10px;">
                <?php echo htmlspecialchars($tarea["descripcion"]); ?></span>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No tienes tareas pendientes.</li>
    <?php endif; ?>
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
<script>
    // Desvanece y luego oculta mensajes después de 3 segundos
    setTimeout(() => {
        const mensajes = document.querySelectorAll("p[style*='font-weight: bold']");
        mensajes.forEach(msg => {
            msg.classList.add("fade-out");
            setTimeout(() => msg.classList.add("hidden"), 1000); // Espera a que se desvanezca
        });
    }, 3000);
</script>
