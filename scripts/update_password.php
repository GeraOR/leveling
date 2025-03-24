<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Verificar que la nueva contraseña tenga al menos 6 caracteres
    if (strlen($new_password) < 6) {
        $_SESSION["error"] = "❌ La nueva contraseña debe tener al menos 6 caracteres.";
        header("Location: ../views/perfil.php");
        exit();
    }

    // Verificar si las nuevas contraseñas coinciden
    if ($new_password !== $confirm_password) {
        $_SESSION["error"] = "❌ Las nuevas contraseñas no coinciden.";
        header("Location: ../views/perfil.php");
        exit();
    }

    // Obtener la contraseña actual del usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($password_hashed);
    $stmt->fetch();
    $stmt->close();

    // Verificar que la contraseña actual ingresada sea correcta
    if (!password_verify($current_password, $password_hashed)) {
        $_SESSION["error"] = "❌ La contraseña actual es incorrecta.";
        header("Location: ../views/perfil.php");
        exit();
    }

    // Encriptar la nueva contraseña y actualizarla en la base de datos
    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_password_hashed, $usuario_id);

    if ($stmt->execute()) {
        $_SESSION["success"] = "✅ Contraseña actualizada correctamente.";
    } else {
        $_SESSION["error"] = "❌ Error al actualizar la contraseña.";
    }

    $stmt->close();
    header("Location: ../views/perfil.php");
    exit();
}
?>
