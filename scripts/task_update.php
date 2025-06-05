<?php
include "../includes/db.php";
session_start();

$id = $_POST['tarea_id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['due_date'];
$importancia = $_POST['importancia'];

// Función para asignar XP según importancia
function obtenerXPporImportancia($importancia) {
    $xp_por_importancia = [
        'alta' => 40,
        'media' => 20,
        'baja' => 10,
        'mínima' => 5
    ];
    return $xp_por_importancia[$importancia] ?? 0;
}

$xp = obtenerXPporImportancia($importancia);

$stmt = $conn->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, importancia = ?, xp_recompensa = ?, fecha_limite = ? WHERE id = ?");
$stmt->bind_param("sssisi", $titulo, $descripcion, $importancia, $xp, $fecha, $id);

if ($stmt->execute()) {
    $_SESSION["tarea_success"] = "Tarea actualizada correctamente.";
} else {
    $_SESSION["tarea_error"] = "Error al actualizar la tarea.";
}

$stmt->close();
$conn->close();

header("Location: ../views/tareas.php");
exit;
