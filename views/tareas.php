<?php
include "../includes/db.php";
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

// 👇 Agrega esto justo después de la sesión y conexión
include "../scripts/verificar_tareas_vencidas.php";

$usuario_id = $_SESSION["usuario_id"];
// Obtener tareas pendientes del usuario
$sql_tareas = "SELECT id, titulo, descripcion, fecha_limite, xp_recompensa, importancia FROM tareas WHERE usuario_id = ? AND estado = 1 ORDER BY importancia ASC";
$stmt = $conn->prepare($sql_tareas);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result_tareas = $stmt->get_result();
$tareas = $result_tareas->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<?php
function obtenerColor($importancia)
{
    return match ($importancia) {
        'alta' => 'background-color: #dc3545;',   // rojo
        'media' => 'background-color: #ffc107;',  // amarillo
        'baja' => 'background-color: #007bff;',   // azul
        'mínima' => 'background-color: #6c757d;', // gris
        default => '',
    };
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/leveling/assets/css/styles.css?v=1.3">
    <link rel="stylesheet" href="/leveling/assets/css/tareas.css?v=1.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Mis Tareas - Solo Leveling</title>
    <style>
        .alerta-vencida {
    background-color: rgba(255, 0, 0, 0.1);
    border: 1px solid #ff4444;
    color: #ff4444;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    white-space: pre-wrap;
    font-weight: bold;
}

    </style>
</head>

<body id="tareas">
    <header>
        <h1>Mis Tareas</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="#tareas">Mis Tareas</a></li>
                <li><a href="../scripts/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
<?php if (isset($_SESSION["penalizacion_info"])): ?>
    <div class="alerta-vencida">
        <pre><?php echo $_SESSION["penalizacion_info"]; ?></pre>
    </div>
    <?php unset($_SESSION["penalizacion_info"]); ?>
<?php endif; ?>

        <section>
            <h2>Agregar Nueva Tarea</h2>
            <?php if (isset($_SESSION["task_success"])): ?>
                <p style="color: green; font-weight: bold;"><?php echo $_SESSION["task_success"]; ?></p>
                <?php unset($_SESSION["task_success"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["task_error"])): ?>
                <p style="color: red; font-weight: bold;"><?php echo $_SESSION["task_error"]; ?></p>
                <?php unset($_SESSION["task_error"]); ?>
            <?php endif; ?>
            <br>
            <form action="../scripts/add_task.php" method="POST" autocomplete="off">
                <label for="titulo">Título de la Tarea:</label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                <div class="input-row">
                    <div class="form-group">
                        <label for="importancia">Importancia:</label>
                        <select id="importancia" name="importancia" required>
                            <option value="alta">🔴 Alta</option>
                            <option value="media">🟡 Media</option>
                            <option value="baja">🔵 Baja</option>
                            <option value="mínima">⚪ Mínima</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="due_date">Fecha límite:</label>
                        <input type="date" id="due_date" name="due_date" required>
                    </div>
                </div>

                <button type="submit">Agregar</button>
            </form>
        </section>

        <section>
            <h2>Lista de Tareas</h2>
            <?php if (isset($_SESSION["tarea_success"])) : ?>
                <p style="color: green; font-weight: bold;"><?php echo $_SESSION["tarea_success"]; ?></p>
                <?php unset($_SESSION["tarea_success"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["tarea_error"])) : ?>
                <p style="color: red; font-weight: bold;"><?php echo $_SESSION["tarea_error"]; ?></p>
                <?php unset($_SESSION["tarea_error"]); ?>
            <?php endif; ?>

            <ul class="task-list">
                <?php if (count($tareas) > 0): ?>
                    <?php foreach ($tareas as $tarea): ?>
                        <li class="task-item">
                            <span class="etiqueta-importancia" style="<?php echo obtenerColor($tarea['importancia']); ?>">
                                <?php echo ucfirst($tarea['importancia']); ?>
                            </span>


                            <div style="flex-grow: 1;">
                                <?php echo htmlspecialchars($tarea["titulo"]); ?>
                            </div>
                            <div style="display: flex; gap: 5px;">
                                <form action="ver_tarea.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                                    <button class="boton-pequeno ver" type="button"
                                        onclick="abrirModalVer('<?php echo addslashes($tarea['titulo']); ?>', '<?php echo addslashes($tarea['descripcion']); ?>', '<?php echo $tarea['fecha_limite']; ?>', '<?php echo addslashes($tarea['xp_recompensa']); ?>')">👁</button>
                                </form>

                                <form action="editar_tarea.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                                    <button class="boton-pequeno editar" type="button"
                                        onclick="abrirModalEditar('<?php echo $tarea['id']; ?>', '<?php echo addslashes($tarea['titulo']); ?>', '<?php echo addslashes($tarea['descripcion']); ?>', '<?php echo $tarea['fecha_limite']; ?>', '<?php echo $tarea['importancia']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </form>

                                <form action="../scripts/marcar_completada.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                                    <button type="submit" class="boton-pequeno marcar" title="Marcar como hecha">✔</button>
                                </form>

                                <form action="../scripts/eliminar_tarea.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres eliminar esta tarea?');">
                                    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                                    <button type="submit" class="boton-pequeno eliminar">🗑</button>
                                </form>

                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No tienes tareas pendientes.</li>
                <?php endif; ?>
            </ul>
        </section>

    </main>
    <!-- Modal Ver Tarea -->
    <div id="modalVer" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarModal('modalVer')">&times;</span>
            <h3 id="verTitulo"></h3>
            <p id="verDescripcion"></p>
            <p><strong>XP recompensa:</strong> <span id="verXpRecompensa"></span></p>
            <p><strong>Fecha límite:</strong> <span id="verFecha"></span></p>
        </div>
    </div>

    <!-- Modal Editar Tarea -->
    <div id="modalEditar" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarModal('modalEditar')">&times;</span>
            <form id="formEditar" method="POST" action="../scripts/task_update.php">
                <input type="hidden" name="tarea_id" id="editarId">
                <label for="editarTitulo">Título:</label>
                <input type="text" id="editarTitulo" name="titulo" required>
                <label for="editarDescripcion">Descripción:</label>
                <textarea id="editarDescripcion" name="descripcion" rows="4" required></textarea>
                <label for="editarImportancia">Importancia:</label>
                <select id="editarImportancia" name="importancia" required>
                    <option value="alta">🔴 Alta</option>
                    <option value="media">🟡 Media</option>
                    <option value="baja">🔵 Baja</option>
                    <option value="mínima">⚪ Mínima</option>
                </select>
                <label for="editarFecha">Fecha límite:</label>
                <input type="date" id="editarFecha" name="due_date">
                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
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
    <script>
        function abrirModalVer(titulo, descripcion, fecha, xpRecompensa) {
            document.getElementById('verTitulo').innerText = titulo;
            document.getElementById('verDescripcion').innerText = descripcion;
            document.getElementById('verXpRecompensa').innerText = xpRecompensa;
            document.getElementById('verFecha').innerText = fecha;
            document.getElementById('modalVer').style.display = 'block';
        }

        function abrirModalEditar(id, titulo, descripcion, fecha, importancia) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarTitulo').value = titulo;
            document.getElementById('editarDescripcion').value = descripcion;
            document.getElementById('editarImportancia').value = importancia;
            document.getElementById('editarFecha').value = fecha;
            document.getElementById('modalEditar').style.display = 'block';
        }

        function cerrarModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>

</html>