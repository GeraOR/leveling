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

    $sql = "DELETE FROM tareas WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    
    if ($stmt->execute()) {
        $_SESSION["tarea_success"] = "Tarea eliminada correctamente.";
    } else {
        $_SESSION["tarea_error"] = "Hubo un error al eliminar la tarea.";
    }

    $stmt->close();
}
// Redirigir a la página anterior
$referer = $_SERVER['HTTP_REFERER'] ?? '../views/dashboard.php';
header("Location: $referer");
exit();
