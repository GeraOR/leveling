<?php
include "../includes/db.php";
session_start();

$id = $_POST['tarea_id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['due_date'];

$stmt = $conn->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ? WHERE id = ?");
$stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $id);

if ($stmt->execute()) {
    $_SESSION["tarea_success"] = "Tarea actualizada correctamente.";
} else {
    $_SESSION["tarea_error"] = "Error al actualizar la tarea.";
}

$stmt->close();
$conn->close();

header("Location: ../views/tareas.php");
exit;
