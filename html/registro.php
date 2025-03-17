<?php
include "../includes/db.php";

$error = ""; // Variable para el mensaje de error
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../includes/db.php"; // Asegúrate de incluir la conexión a la base de datos

    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Verificar si las contraseñas coinciden
    if ($password !== $password_confirm) {
        $error = "❌ Las contraseñas no coinciden.";
    } else {
        // Encriptar la contraseña antes de guardarla
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Preparar la consulta SQL
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION["success"] = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
            header("Location: ../index.php");
            exit();
        } else {
            $error = "❌ Error en el registro: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/registro.css">
    <title>Registro - Solo Leveling</title>
</head>
<body>
    <div class="register-container">
        <h2>Crear Cuenta</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirm">Confirmar Contraseña:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <?php if (!empty($error)) : ?>
                <p style="color: red;" class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="../index.php">Inicia sesión</a></p>
    </div>
</body>
</html>
