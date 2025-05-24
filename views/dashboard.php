<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
// Obtener datos del usuario
$sql = "SELECT nombre, email, foto, nivel, xp, rango FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Obtener tareas pendientes del usuario
$sql_tareas = "SELECT id, titulo, descripcion FROM tareas WHERE usuario_id = ? AND estado = 1 ORDER BY id ASC LIMIT 3";
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
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/dashboard.css?v=1.1">
    <title>Dashboard - Solo Leveling</title>
    <style>
        
.task-mark{
            background-color: #4CAF50;
            border: none;
            color: white;
                padding: 4px 8px;
                font-size: 14px;
                margin-top: auto;
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
.task-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border-bottom: 1px solid #00eaff;
}
.boton-pequeno {
    font-size: 14px;
    padding: 4px 8px;
    margin-top: auto;
    margin-right: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.boton-pequeno.eliminar {
    background-color: #dc3545;
    color: white;
}

.boton-pequeno.eliminar:hover {
    background-color: #bd2130;
}

    </style>
</head>
<body id="inicio">
    <header>
        <h1>Bienvenido a Solo Leveling</h1>
        <nav>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
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
                <p><strong>Nivel:</strong> <span><?php echo $usuario["nivel"]; ?></span></p>
                <p><strong>Experiencia:</strong> <span><?php echo $usuario["xp"]; ?>/100</span></p>
                <p><strong>Rango:</strong> <span><?php echo $usuario["rango"]; ?></span></p>
            </div>
        </section>
        <section>
        
            <h2>Tareas Pendientes</h2>
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
                <form action="../scripts/marcar_completada.php" method="POST" style="display:inline;">
                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                    <button type="submit" class="task-mark"
            title="Marcar como hecha">✔</button>
                </form>
                <?php echo htmlspecialchars($tarea["titulo"]); ?>
                <form action="../scripts/eliminar_tarea.php" method="POST" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres eliminar esta tarea?');">
            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
            <button type="submit" class="boton-pequeno eliminar">🗑</button>
        </form>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No tienes tareas pendientes.</li>
    <?php endif; ?>
</ul>

            <a href="tareas.php" class="task-link">Ver todas las tareas</a>
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
