<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tarea_id"])) {
    $tarea_id = $_POST["tarea_id"];
    $usuario_id = $_SESSION["usuario_id"];

    // 1. Obtener la XP de la tarea (y confirmar que pertenece al usuario)
    $sql = "SELECT xp_recompensa FROM tareas WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    $stmt->execute();
    $stmt->bind_result($xp_tarea);
    $stmt->fetch();
    $stmt->close();

    if (isset($xp_tarea)) {
        // 2. Marcar la tarea como completada
        $sql = "UPDATE tareas SET estado = 'completada' WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $tarea_id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        // 3. Sumar la XP al usuario
        $sql = "UPDATE usuarios SET xp = xp + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $xp_tarea, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION["tarea_success"] = "¡Tarea completada! Ganaste $xp_tarea XP.";
    } else {
        $_SESSION["tarea_error"] = "Tarea no encontrada o no pertenece al usuario.";
    }
}

// Redirigir a la página anterior
$referer = $_SERVER['HTTP_REFERER'] ?? '../views/dashboard.php';
header("Location: $referer");
exit();
