<?php
include "../includes/db.php";

// Función para calcular rango
function obtenerRango($nivel)
{
    if ($nivel >= 280) return "Clase S+";
    elseif ($nivel >= 210) return "Clase S";
    elseif ($nivel >= 150) return "Clase A";
    elseif ($nivel >= 100) return "Clase B";
    elseif ($nivel >= 60)  return "Clase C";
    elseif ($nivel >= 30)  return "Clase D";
    elseif ($nivel >= 10)  return "Clase E";
    else                   return "Clase F";
}

$hoy = date("Y-m-d");

$sql = "SELECT id, usuario_id, xp_recompensa FROM tareas WHERE fecha_limite < ? AND estado = 'pendiente'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hoy);
$stmt->execute();
$result = $stmt->get_result();

$tareasVencidas = 0;
$penalizaciones = [];

while ($tarea = $result->fetch_assoc()) {
    $tarea_id = $tarea["id"];
    $usuario_id = $tarea["usuario_id"];
    $xp_penalizacion = $tarea["xp_recompensa"];

    // Obtener nivel, xp y rango del usuario
    $sql_user = "SELECT xp, nivel, rango FROM usuarios WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $usuario_id);
    $stmt_user->execute();
    $stmt_user->bind_result($xp_actual, $nivel_actual, $rango_actual);
    $stmt_user->fetch();
    $stmt_user->close();

    $xp_restante = $xp_actual - $xp_penalizacion;
    $nivel_inicial = $nivel_actual;
    $rango_inicial = $rango_actual;

    // Bajar de nivel si es necesario
    while ($xp_restante < 0 && $nivel_actual > 0) {
        $nivel_actual--;
        $xp_restante += 100;
    }

    $xp_nuevo = max(0, $xp_restante);
    $nuevo_rango = obtenerRango($nivel_actual);

    // Actualizar datos del usuario
    $sql_upd = "UPDATE usuarios SET xp = ?, nivel = ?, rango = ? WHERE id = ?";
    $stmt_upd = $conn->prepare($sql_upd);
    $stmt_upd->bind_param("iisi", $xp_nuevo, $nivel_actual, $nuevo_rango, $usuario_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // Marcar tarea como vencida
    $update_sql = "UPDATE tareas SET estado = 'vencida' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $tarea_id);
    $update_stmt->execute();
    $update_stmt->close();

    $tareasVencidas++;

    // Guardar penalización para mensaje
    $penalizaciones[] = [
        "usuario_id" => $usuario_id,
        "xp_perdida" => $xp_penalizacion,
        "nivel_bajo" => $nivel_actual < $nivel_inicial,
        "nuevo_nivel" => $nivel_actual,
        "rango_cambio" => $nuevo_rango !== $rango_inicial,
        "nuevo_rango" => $nuevo_rango,
    ];
}

$stmt->close();

// Construir mensaje de penalización
if ($tareasVencidas > 0) {
    $_SESSION["penalizacion_info"] = "⚠️ Se vencieron $tareasVencidas tareas:\n\n";

    foreach ($penalizaciones as $p) {
        $mensaje = "🔻 Usuario ID {$p['usuario_id']} perdió {$p['xp_perdida']} XP";
        if ($p["nivel_bajo"]) {
            $mensaje .= ", bajó a nivel {$p['nuevo_nivel']}";
        } else {
            $mensaje .= ", ahora está en nivel {$p['nuevo_nivel']}";
        }

        if ($p["rango_cambio"]) {
            $mensaje .= " (nuevo rango: {$p['nuevo_rango']})";
        }

        $_SESSION["penalizacion_info"] .= $mensaje . ".\n";
    }
}
