<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto"])) {
    $foto = $_FILES["foto"];

    // Validaciones
    $allowed_types = ["image/jpeg", "image/png", "image/gif"];
    if (!in_array($foto["type"], $allowed_types)) {
        $_SESSION["foto_error"] = "Solo se permiten imágenes JPG, PNG y GIF.";
        header("Location: ../views/perfil.php");
        exit();
    }

    if ($foto["size"] > 2 * 1024 * 1024) { // 2MB
        $_SESSION["foto_error"] = "La imagen es demasiado grande. Máximo 2MB.";
        header("Location: ../views/perfil.php");
        exit();
    }

    // Nombre único para la imagen
    $ext = pathinfo($foto["name"], PATHINFO_EXTENSION);
    $nuevo_nombre = "foto_" . $usuario_id . "_" . time() . "." . $ext;
    $ruta_destino = "../uploads/" . $nuevo_nombre;

    // Mover el archivo a la carpeta de uploads
    if (move_uploaded_file($foto["tmp_name"], $ruta_destino)) {
        // Guardar la nueva foto en la base de datos
        $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nuevo_nombre, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION["foto"] = $nuevo_nombre;
            $_SESSION["foto_success"] = "¡Foto actualizada con éxito!";
        } else {
            $_SESSION["foto_error"] = "Error al guardar en la base de datos.";
        }
        $stmt->close();
    } else {
        $_SESSION["foto_error"] = "Error al subir la imagen.";
    }
}

header("Location: ../views/perfil.php");
exit();
?>
