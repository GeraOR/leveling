<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    // Verificar si el nuevo correo ya está registrado en otro usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $usuario_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION["perfil_error"] = "❌ Este correo ya está en uso por otro usuario.";
        header("Location: ../views/perfil.php#perfil");
        exit();
    }

    $stmt->close();

    // Actualizar los datos del usuario
    $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $email, $usuario_id);

    if ($stmt->execute()) {
        $_SESSION["perfil_success"] = "✅ Perfil actualizado correctamente.";
    } else {
        $_SESSION["perfil_error"] = "❌ Error al actualizar el perfil.";
    }

    $stmt->close();
    header("Location: ../views/perfil.php#profile");
    exit();
}
