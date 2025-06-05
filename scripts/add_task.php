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

if ($titulo && $descripcion) {
    $sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha_limite, importancia, xp_recompensa)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $usuario_id, $titulo, $descripcion, $fecha_limite, $importancia, $xp);

    if ($stmt->execute()) {
        $_SESSION["task_success"] = "Tarea agregada correctamente. Esta tarea vale $xp XP.";
    } else {
        $_SESSION["task_error"] = "Error al agregar la tarea.";
    }

    $stmt->close();
} else {
    $_SESSION["task_error"] = "El título y la descripción son obligatorios.";
}

header("Location: ../views/tareas.php");
exit();
