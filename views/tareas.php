<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
$usuario_id = $_SESSION["usuario_id"];
// Obtener tareas pendientes del usuario
$sql_tareas = "SELECT id, titulo, descripcion FROM tareas WHERE usuario_id = ? AND estado = 1 ORDER BY id ASC";
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
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.2">
    <link rel="stylesheet" href="/leveling/assets/css/tareas.css?v=1.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Mis Tareas - Solo Leveling</title>
</head>
<body id="tareas">
    <header>
        <h1>Mis Tareas</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="#tareas">Mis Tareas</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
    <h2>Agregar Nueva Tarea</h2>
    <?php if (isset($_SESSION["task_success"])): ?>
    <p style="color: green; font-weight: bold;"><?php echo $_SESSION["task_success"]; ?></p>
    <?php unset($_SESSION["task_success"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["task_error"])): ?>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION["task_error"]; ?></p>
    <?php unset($_SESSION["task_error"]); ?>
<?php endif; ?>

    <form action="../scripts/add_task.php" method="POST" autocomplete="off">
        <label for="titulo">Título de la Tarea:</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

        <label for="due_date">Fecha límite:</label>
        <input type="date" id="due_date" name="due_date">

        <button type="submit">Agregar</button>
    </form>
</section>

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
                <li class="task-item">
                    <div style="flex-grow: 1;">
                        <?php echo htmlspecialchars($tarea["titulo"]); ?>
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <form action="ver_tarea.php" method="GET" style="display:inline;">
                            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                            <button type="submit" class="boton-pequeno ver" title="Ver tarea">👁</button>
                        </form>

                        <form action="editar_tarea.php" method="GET" style="display:inline;">
                            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
<button type="submit" class="boton-pequeno editar" title="Editar tarea">
    <i class="fas fa-edit"></i>
</button>
                        </form>

                        <form action="../scripts/marcar_completada.php" method="POST" style="display:inline;">
                            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                            <button type="submit" class="boton-pequeno marcar" title="Marcar como hecha">✔</button>
                        </form>

                        <form action="../scripts/eliminar_tarea.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres eliminar esta tarea?');">
                            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                            <button type="submit" class="boton-pequeno eliminar">🗑</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No tienes tareas pendientes.</li>
        <?php endif; ?>
    </ul>
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
