<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_FILES["foto"]["error"] == 0) {
    $nombreArchivo = "perfil_" . $usuario_id . "_" . time() . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
        // Guardar la nueva foto en la base de datos
        $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreArchivo, $usuario_id);
        $stmt->execute();
        $stmt->close();

        // Actualizar la sesión
        $_SESSION["foto"] = $nombreArchivo;
    }
}

header("Location: ../views/perfil.php");
exit();
?>
