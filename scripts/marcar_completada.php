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

    // Asegurar que la tarea pertenece al usuario
    $sql = "UPDATE tareas SET estado = 0 WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    if ($stmt->execute()) {
        $_SESSION["tarea_success"] = "¡Tarea completada con éxito!";
    } else {
        $_SESSION["tarea_error"] = "Hubo un error al marcar la tarea.";
    }
    $stmt->close();
}

header("Location: ../views/dashboard.php");
exit();
