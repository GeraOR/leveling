<?php
include "../includes/db.php";
session_start();
$mensaje = "";
$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Verificar si el correo ya existe
    $sql_check = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $mensaje = "<div class='error' style='color:red;'>❌ Este correo ya está registrado. Intenta con otro.</div>";
    } elseif (strlen($password) < 6) {
        $mensaje = "<div class='error' style='color:red;'>❌ La contraseña debe tener al menos 6 caracteres.</div>";
    } elseif ($password !== $password_confirm) {
        $mensaje = "<div class='error' style='color:red;'>❌ Las contraseñas no coinciden.</div>";
    } else {
        // Encriptar la contraseña
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insertar en la base de datos
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
    $stmt_check->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/registro.css?v=1.0">
    <title>Registro - Solo Leveling</title>
</head>

<body>
    <div class="register-container">
        <h2>Crear Cuenta</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="password">Contraseña: <img src="../img/ojo_cerrado.png" class="toggle-password" onclick="togglePassword('password', this)" alt="Mostrar contraseña" style="cursor: pointer; width: 20px; margin: 0 3px -4px;"></label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirm">Confirmar Contraseña: <img src="../img/ojo_cerrado.png" class="toggle-password" onclick="togglePassword('password_confirm', this)" alt="Mostrar contraseña" style="cursor: pointer; width: 20px; margin: 0 3px -4px;"></label>
            <input type="password" id="password_confirm" name="password_confirm" required>

            <?php if (!empty($mensaje)) : ?>
                <p style="color: red;" class="error"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="../index.php">Inicia sesión</a></p>
    </div>
    <script>
        function togglePassword(fieldId, img) {
            let input = document.getElementById(fieldId);

            if (input.type === "password") {
                input.type = "text";
                img.src = "../img/ojo.png"; // Cambia al ojo cerrado
            } else {
                input.type = "password";
                img.src = "../img/ojo_cerrado.png"; // Cambia al ojo abierto
            }
        }
    </script>

</body>

</html>