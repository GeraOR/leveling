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
$sql_tareas = "SELECT id, descripcion FROM tareas WHERE usuario_id = ? AND estado = 1 ORDER BY id DESC LIMIT 5";
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
                <p><strong>Nivel:</strong> <span><?php echo $usuario["nivel"]; ?></span></p>
                <p><strong>Experiencia:</strong> <span><?php echo $usuario["xp"]; ?>/100</span></p>
                <p><strong>Rango:</strong> <span><?php echo $usuario["rango"]; ?></span></p>
            </div>
        </section>
        <section>
            <h2>Tareas Pendientes</h2>
            <ul class="task-list">
    <?php if (count($tareas) > 0): ?>
        <?php foreach ($tareas as $tarea): ?>
            <li>
                <?php echo htmlspecialchars($tarea["descripcion"]); ?>
                <form action="../scripts/marcar_completada.php" method="POST" style="display:inline;">
                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                    <button type="submit">✔</button>
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
