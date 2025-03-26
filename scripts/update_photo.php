<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto"])) {
    $foto = $_FILES["foto"];
    $extension = pathinfo($foto["name"], PATHINFO_EXTENSION); // Obtener la extensión del archivo
    $nombreFoto = "user_" . $usuario_id . "_" . time() . "." . $extension;
    $rutaDestino = "../uploads/" . $nombreFoto;

    if (move_uploaded_file($foto["tmp_name"], $rutaDestino)) {
        // Obtener la foto actual del usuario
        $sql = "SELECT foto FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();

        // Eliminar la foto anterior si no es la default
        if (!empty($usuario["foto"]) && $usuario["foto"] !== "default.png") {
            $rutaFotoAntigua = "../uploads/" . $usuario["foto"];
            if (file_exists($rutaFotoAntigua)) {
                unlink($rutaFotoAntigua);
            }
        }

        // Actualizar la nueva foto en la base de datos
        $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreFoto, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION["foto"] = $nombreFoto;
        $_SESSION["foto_success"] = "Foto de perfil actualizada con éxito.";
    } else {
        $_SESSION["foto_error"] = "Hubo un error al subir la foto.";
    }
}

header("Location: ../views/perfil.php");
exit();
?>
