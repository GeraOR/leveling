<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
$titulo = trim($_POST["titulo"]);
$descripcion = trim($_POST["descripcion"]);
$fecha_limite = !empty($_POST["due_date"]) ? $_POST["due_date"] : null;

if ($titulo && $descripcion) {
    $sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha_limite) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $usuario_id, $titulo, $descripcion, $fecha_limite);

    if ($stmt->execute()) {
        $_SESSION["task_success"] = "Tarea agregada correctamente.";
    } else {
        $_SESSION["task_error"] = "Error al agregar la tarea.";
    }

    $stmt->close();
} else {
    $_SESSION["task_error"] = "El título y la descripción son obligatorios.";
}

header("Location: ../views/tareas.php");
exit();
