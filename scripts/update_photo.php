<?php
include "../includes/db.php";
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto"])) {
    $usuario_id = $_SESSION["usuario_id"];
    $foto_nombre = "user_" . $usuario_id . "_" . time() . ".png";
    $ruta = "../uploads/" . $foto_nombre;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta)) {
        $query = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
        $query->execute([$foto_nombre, $usuario_id]);
        $_SESSION["foto"] = $foto_nombre;
    }
}

header("Location: ../views/perfil.php#foto-perfil");
exit();
?>
