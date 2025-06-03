<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
$puntos = match ($tarea['importancia']) {
    'alta' => 40,
    'media' => 20,
    'baja' => 10,
    'mínima' => 5,
    default => 0,
};

// Lógica para sumar puntos a tu usuario...

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tarea_id"])) {
    $tarea_id = $_POST["tarea_id"];
    $usuario_id = $_SESSION["usuario_id"];

    // Asegurar que la tarea pertenece al usuario
    $sql = "UPDATE tareas SET estado = 'completada' WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    if ($stmt->execute()) {
        $_SESSION["tarea_success"] = "¡Tarea completada con éxito!";
    } else {
        $_SESSION["tarea_error"] = "Hubo un error al marcar la tarea.";
    }
    $stmt->close();
}

// Redirigir a la página anterior
$referer = $_SERVER['HTTP_REFERER'] ?? '../views/dashboard.php';
header("Location: $referer");
exit();
