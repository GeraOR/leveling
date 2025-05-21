<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener datos del usuario
$sql = "SELECT nombre, email, foto, nivel, xp, rango FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
$foto = !empty($usuario["foto"]) ? $usuario["foto"] : "default.png";

// Actualizar la sesión con la foto
$_SESSION["foto"] = $foto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/perfil.css?v=1.0">
    <title>Perfil - Solo Leveling</title>
    <style>
        .fade-out {
    opacity: 1;
    transition: opacity 1s ease-out;
}

.fade-out.hidden {
    opacity: 0;
}
    </style>
</head>
<body  id="perfil">
    <header>
        <h1>Perfil de Usuario</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="#perfil">Perfil</a></li>
                <li><a href="tareas.php">Mis Tareas</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Información Personal</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario["nombre"]); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($usuario["email"]); ?></p>
        </section>
        <section id="photo">
            <h2>Foto de Perfil</h2>
            <img src="../uploads/<?php echo htmlspecialchars($_SESSION["foto"]); ?>?t=<?php echo time(); ?>" 
                 alt="Foto de perfil" width="150">

            <?php if (isset($_SESSION["foto_success"])) : ?>
                <p style="color: green; font-weight: bold;"><?php echo $_SESSION["foto_success"]; ?></p>
                <?php unset($_SESSION["foto_success"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["foto_error"])) : ?>
                <p style="color: red; font-weight: bold;"><?php echo $_SESSION["foto_error"]; ?></p>
                <?php unset($_SESSION["foto_error"]); ?>
            <?php endif; ?>

            <form action="../scripts/update_photo.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="foto" accept="image/*" required>
                <button type="submit">Actualizar Foto</button>
            </form>
        </section>

        <section>
            <h2>Progreso</h2>
            <div class="progreso">
                <p><strong>Nivel:</strong> <span><?php echo $usuario["nivel"]; ?></span></p>
                <p><strong>Experiencia:</strong> <span><?php echo $usuario["xp"]; ?>/100</span></p>
                <p><strong>Rango:</strong> <span><?php echo $usuario["rango"]; ?></span></p>
                <form action="../scripts/update_xp.php" method="POST" style="margin-top: 20px;">
    <label for="xp">Sumar experiencia:</label>
    <input type="number" name="xp" id="xp" min="1" required>
    <button type="submit">Ganar XP</button>
</form>

<?php if (isset($_SESSION["xp_success"])) : ?>
    <p style="color: green; font-weight: bold;"><?php echo $_SESSION["xp_success"]; ?></p>
    <?php unset($_SESSION["xp_success"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["xp_error"])) : ?>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION["xp_error"]; ?></p>
    <?php unset($_SESSION["xp_error"]); ?>
<?php endif; ?>

            </div>
        </section>

        <section>
            <h2>Gestión Personal</h2>
            <a href="motivacion.php" class="task-link">Ver Motivaciones</a>
            <a href="penalizaciones.php" class="task-link">Ver Penalizaciones</a>
        </section>
        
        <section id="profile">
            <h2>Editar Perfil</h2>
            <form action="../scripts/update_profile.php" method="POST">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario["email"]); ?>" required>
                <?php if (isset($_SESSION["perfil_success"])) : ?>
    <p style="color: green; font-weight: bold;"><?php echo $_SESSION["perfil_success"]; ?></p>
    <?php unset($_SESSION["perfil_success"]);
    endif; ?>
    <?php if (isset($_SESSION["perfil_error"])) : ?>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION["perfil_error"]; ?></p>
    <?php unset($_SESSION["perfil_error"]);
    endif; ?>
                <button type="submit">Guardar Cambios</button>
            </form>
        </section>

        <section id="password">
            <h2>Cambiar Contraseña</h2>
            <form action="../scripts/update_password.php" method="POST">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($_SESSION["success"])) : ?>
    <p style="color: green; font-weight: bold;"><?php echo $_SESSION["success"]; ?></p>
    <?php unset($_SESSION["success"]);
    endif; ?>
    <?php if (isset($_SESSION["error"])) : ?>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION["error"]; ?></p>
    <?php unset($_SESSION["error"]);
    endif; ?>
                <button type="submit">Cambiar Contraseña</button>
            </form>
        </section>
    </main>
</body>
</html>
<script>
    // Desvanece y luego oculta mensajes después de 3 segundos
    setTimeout(() => {
        const mensajes = document.querySelectorAll("p[style*='font-weight: bold']");
        mensajes.forEach(msg => {
            msg.classList.add("fade-out");
            setTimeout(() => msg.classList.add("hidden"), 1000); // Espera a que se desvanezca
        });
    }, 3000);
</script>