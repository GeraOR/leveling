<?php
include "includes/db.php";

session_start();
$mensaje = "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, foto, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nombre,$foto, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION["usuario_id"] = $id;
        $_SESSION["usuario_nombre"] = $nombre;
        $_SESSION["foto"] = $usuario["foto"] ?? 'default.png';
        header("Location: views/dashboard.php");
        exit();
    } else {
        $mensaje=  "<div class='error' style='color:red;'>❌ Correo Electrónico o contraseña incorrectos.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/index.css?v=1.0">
    <title>Login - Solo Leveling</title>
</head>
<body>
    <div class="login-container">
    <?php if (isset($_SESSION["success"])) : ?>
    <p style="color: green;"><?php echo $_SESSION["success"]; ?></p>
    <?php unset($_SESSION["success"]); // Eliminar mensaje después de mostrarlo ?>
<?php endif; ?>
        <h2>Iniciar Sesión</h2>
        <form method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            
            <label for="password">Contraseña: <img src="img/ojo_cerrado.png" class="toggle-password" onclick="togglePassword('password', this)" alt="Mostrar contraseña" style="cursor: pointer; width: 20px; margin: 0 3px -4px;"></label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($mensaje)) : ?>
                <p style="color: red;" class="error"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tienes cuenta? <a href="views/registro.php">Regístrate</a></p>
    </div>
    <script>
        function togglePassword(fieldId, img) {
            let input = document.getElementById(fieldId);

            if (input.type === "password") {
                input.type = "text";
                img.src = "img/ojo.png"; // Cambia al ojo cerrado
            } else {
                input.type = "password";
                img.src = "img/ojo_cerrado.png"; // Cambia al ojo abierto
            }
        }
    </script>
</body>
</html>
