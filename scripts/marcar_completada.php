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

    // 1. Obtener la XP de la tarea
    $sql = "SELECT xp_recompensa FROM tareas WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    $stmt->execute();
    $stmt->bind_result($xp_tarea);
    $stmt->fetch();
    $stmt->close();

    if (isset($xp_tarea)) {
        // 2. Obtener datos actuales del usuario
        $sql = "SELECT xp, nivel, rango FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();

        $xpActual = intval($usuario['xp']);
        $nivelActual = intval($usuario['nivel']);
        $xpTotal = $xpActual + intval($xp_tarea);

        // 3. Subir de nivel por cada 100 xp
        while ($xpTotal >= 100) {
            $nivelActual++;
            $xpTotal -= 100;
        }

        // 4. Determinar nuevo rango
        function obtenerRango($nivel) {
            if ($nivel >= 280) return "Clase S+";
            if ($nivel >= 210) return "Clase S";
            if ($nivel >= 150) return "Clase A";
            if ($nivel >= 100) return "Clase B";
            if ($nivel >= 60)  return "Clase C";
            if ($nivel >= 30)  return "Clase D";
            if ($nivel >= 10)  return "Clase E";
            return "Clase F";
        }

        $nuevoRango = obtenerRango($nivelActual);

        // 5. Marcar la tarea como completada
        $sql = "UPDATE tareas SET estado = 'completada' WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $tarea_id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        // 6. Actualizar la tabla usuarios con nueva xp, nivel y rango
        $sql = "UPDATE usuarios SET xp = ?, nivel = ?, rango = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $xpTotal, $nivelActual, $nuevoRango, $usuario_id);
        $stmt->execute();
        $stmt->close();

        // 7. Mensaje de éxito
        $_SESSION["tarea_success"] = "¡Tarea completada! Ganaste $xp_tarea XP.";
        if ($nivelActual > $usuario["nivel"]) {
            $_SESSION["tarea_success"] .= " Subiste al nivel $nivelActual.";
        }
        if ($nuevoRango !== $usuario["rango"]) {
            $_SESSION["tarea_success"] .= " ¡Nuevo rango: $nuevoRango!";
        }

    } else {
        $_SESSION["tarea_error"] = "Tarea no encontrada o no pertenece al usuario.";
    }
}

// Redirigir a la página anterior
$referer = $_SERVER['HTTP_REFERER'] ?? '../views/dashboard.php';
header("Location: $referer");
exit();
