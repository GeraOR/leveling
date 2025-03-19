<?php
include "../includes/db.php";
session_start();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Validar si la contraseña tiene al menos 6 caracteres
    if (strlen($password) < 6) {
        $mensaje = "<div class='error' style='color:red;'>❌ La contraseña debe tener al menos 6 caracteres.</div>";
    } elseif ($password !== $password_confirm) {
        $mensaje = "<div class='error' style='color:red;'>❌ Las contraseñas no coinciden.</div>";
    } else {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $password_hashed);

        if ($stmt->execute()) {
            $_SESSION["success"] = "✅ Registro exitoso. Inicia sesión.";
            header("Location: ../index.php");
            exit();
        } else {
            $mensaje = "<div class='error' style='color:red;'>❌ Error en el registro: " . $conn->error . "</div>";
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
            
            <label for="password">Contraseña: <span class="toggle-password" onclick="togglePassword('password')">👁️</span></label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
            </div>
            
            <label for="password_confirm">Confirmar Contraseña: <span class="toggle-password" onclick="togglePassword('password_confirm')">👁️</span></label>
            <div class="password-container">
                <input type="password" id="password_confirm" name="password_confirm" required>
                
            </div>

            <?php if (!empty($mensaje)) : ?>
                <p style="color: red;" class="error"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="../index.php">Inicia sesión</a></p>
    </div>
    <script>
function togglePassword(id) {
    var input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>

</body>
</html>
