<?php
include "includes/db.php";

if ($conn) {
    echo "✅ Conexión exitosa a la base de datos";
} else {
    echo "❌ Error de conexión";
}
?>
