<?php
// Configuración de la base de datos
$host = "localhost";  // Servidor de la base de datos
$user = "root";       // Usuario (cámbialo si es diferente)
$password = "";       // Contraseña (déjalo vacío si no tiene)
$database = "leveling"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Opcional: Configurar para que use UTF-8
$conn->set_charset("utf8");
