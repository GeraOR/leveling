<?php
include "includes/db.php";

session_start();
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nombre, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION["usuario_id"] = $id;
        $_SESSION["usuario_nombre"] = $nombre;
        header("Location: html/dashboard.php");
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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
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
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($mensaje)) : ?>
                <p style="color: red;" class="error"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tienes cuenta? <a href="html/registro.php">Regístrate</a></p>
    </div>
</body>
</html>
