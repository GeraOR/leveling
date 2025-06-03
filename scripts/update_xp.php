<?php
session_start();
include "../includes/db.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
$xpGanada = isset($_POST["xp"]) ? intval($_POST["xp"]) : 0;

if ($xpGanada <= 0) {
    $_SESSION["xp_error"] = "La cantidad de experiencia debe ser mayor que 0.";
    header("Location: ../views/perfil.php");
    exit();
}

// Obtener nivel y xp actual del usuario
$sql = "SELECT nivel, xp FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$xpActual = $usuario["xp"];
$nivelActual = $usuario["nivel"];
$xpTotal = $xpActual + $xpGanada;

// Aumentar nivel cada 100 xp
while ($xpTotal >= 100) {
    $nivelActual++;
    $xpTotal -= 100;
}

// Calcular nuevo rango
function obtenerRango($nivel)
{
    if ($nivel >= 280) {
        return "Clase S+";
    } elseif ($nivel >= 210) {
        return "Clase S";
    } elseif ($nivel >= 150) {
        return "Clase A";
    } elseif ($nivel >= 100) {
        return "Clase B";
    } elseif ($nivel >= 60) {
        return "Clase C";
    } elseif ($nivel >= 30) {
        return "Clase D";
    } elseif ($nivel >= 10) {
        return "Clase E";
    } else {
        return "Clase F";
    }
}

$nuevoRango = obtenerRango($nivelActual);

// Actualizar nivel, xp y rango en base de datos
$sql = "UPDATE usuarios SET xp = ?, nivel = ?, rango = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $xpTotal, $nivelActual, $nuevoRango, $usuario_id);
$stmt->execute();
$stmt->close();

// Mensajes para feedback
$_SESSION["xp_success"] = "¡Has ganado $xpGanada XP! ";
if ($nivelActual > $usuario["nivel"]) {
    $_SESSION["xp_success"] .= "Subiste a nivel $nivelActual. ";
}
if ($nuevoRango !== $usuario["rango"]) {
    $_SESSION["xp_success"] .= "¡Nuevo rango: $nuevoRango!";
}

header("Location: ../views/perfil.php");
exit();
