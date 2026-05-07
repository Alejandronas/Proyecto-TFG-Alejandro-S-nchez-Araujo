<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/CitaModel.php';

$modelo     = new CitaModel($pdo);
$todasCitas = $modelo->obtenerTodasCitasMedico($_SESSION['id_empleado']);
$pacientes  = $modelo->obtenerPacientes();

$filtroEstado = $_GET['estado'] ?? '';
$filtroDesde  = $_GET['desde']  ?? '';
$filtroHasta  = $_GET['hasta']  ?? '';

$citasFiltradas = array_filter($todasCitas, function($cita) use ($filtroEstado, $filtroDesde, $filtroHasta) {
    if ($filtroEstado && $cita['estado'] !== $filtroEstado) return false;
    if ($filtroDesde  && $cita['fecha_cita'] < $filtroDesde)  return false;
    if ($filtroHasta  && $cita['fecha_cita'] > $filtroHasta)  return false;
    return true;
});

$colores = ['#0a6e5c','#12907a','#2ec4a5','#c8903a','#d95f5f','#5a9e8f'];
?>

<link rel="stylesheet" href="/assets/css/panel_medico.css">
<link rel="stylesheet" href="/assets/css/citas_medico.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<?php
$pagina_activa = 'citas';
require_once __DIR__ . '/../../includes/sidebar_medico.php';
?>

<main class="panel-principal">

    <div class="cabecera">
        <h1>Mis Citas</h1>
        <div class="cabecera__fecha">
            <i class="bi bi-calendar3"></i>
            <span><?= date('d/m/Y') ?></span>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="/citas.php">
        <div class="filtros-card">
            <div>
                <label>Desde</label>
                <input type="date" name="desde" value="<?= htmlspecialchars($filtroDesde) ?>">
            </div>
            <div>
                <label>Hasta</label>
                <input type="date" name="hasta" value="<?= htmlspecialchars($filtroHasta) ?>">
            </div>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="programada" <?= $filtroEstado === 'programada' ? 'selected' : '' ?>>Programada</option>
                    <option value="completada" <?= $filtroEstado === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="cancelada"  <?= $filtroEstado === 'cancelada'  ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <button type="submit" class="btn-filtrar">
                <i class="bi bi-search"></i> Filtrar
            </button>
            <a href="/citas.php" class="btn-limpiar">Limpiar</a>

            <!-- Botón nueva cita alineado a la derecha -->
            <button type="button" class="btn-nueva-cita ms-auto" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                <i class="bi bi-plus-lg"></i> Nueva Cita
            </button>
        </div>
    </form>

    <!-- Tabla -->
    <div class="tabla-card">
        <h5>Total: <?= count($citasFiltradas) ?> citas</h5>

        <?php if (empty($citasFiltradas)): ?>
            <div class="sin-resultados">
                <i class="bi bi-calendar-x"></i>
                No se encontraron citas con los filtros aplicados.
            </div>
        <?php else: ?>
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citasFiltradas as $i => $cita):
                        $iniciales = strtoupper(substr($cita['nombre'],0,1) . substr($cita['apellido'],0,1));
                        $color     = $colores[$i % count($colores)];
                    ?>
                    <tr>
                        <td>
                            <div class="paciente-cell">
                                <div class="mini-avatar" style="background:<?= $color ?>"><?= $iniciales ?></div>
                                <?= htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']) ?>
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                        <td><?= substr($cita['hora_cita'], 0, 5) ?></td>
                        <td><span class="badge-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span></td>
                        <td>
                            <div class="acciones-cell">
                                <!-- Botón editar: abre modal y rellena con datos de esta fila -->
                                <button class="btn-accion btn-editar"
                                    onclick="abrirModalEditar(
                                        <?= $cita['id_cita'] ?>,
                                        '<?= $cita['fecha_cita'] ?>',
                                        '<?= substr($cita['hora_cita'],0,5) ?>',
                                        '<?= $cita['estado'] ?>'
                                    )">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <!-- Botón eliminar: pide confirmación antes de enviar -->
                                <button class="btn-accion btn-eliminar"
                                    onclick="confirmarEliminar(<?= $cita['id_cita'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>


<!-- ═══════════════════════════════════════════════════════════════
     MODAL — NUEVA CITA
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cita-modal">
            <div class="modal-header cita-modal__header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/controllers/CitaController.php?accion=guardar">
                <div class="modal-body cita-modal__body">
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Paciente</label>
                        <select name="id_paciente" class="form-select cita-modal__input" required>
                            <option value="">Selecciona un paciente…</option>
                            <?php foreach ($pacientes as $p): ?>
                                <option value="<?= $p['id_paciente'] ?>">
                                    <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Fecha</label>
                        <input type="date" name="fecha_cita" class="form-control cita-modal__input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Hora</label>
                        <input type="time" name="hora_cita" class="form-control cita-modal__input" required>
                    </div>
                </div>
                <div class="modal-footer cita-modal__footer">
                    <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     MODAL — EDITAR CITA
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditarCita" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cita-modal">
            <div class="modal-header cita-modal__header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/controllers/CitaController.php?accion=actualizar">
                <input type="hidden" name="id_cita" id="edit-id">
                <div class="modal-body cita-modal__body">
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Fecha</label>
                        <input type="date" name="fecha_cita" id="edit-fecha" class="form-control cita-modal__input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Hora</label>
                        <input type="time" name="hora_cita" id="edit-hora" class="form-control cita-modal__input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Estado</label>
                        <select name="estado" id="edit-estado" class="form-select cita-modal__input">
                            <option value="programada">Programada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer cita-modal__footer">
                    <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Formulario oculto para eliminar (se envía via JavaScript) -->
<form id="form-eliminar" method="POST" action="/controllers/CitaController.php?accion=eliminar" style="display:none">
    <input type="hidden" name="id_cita" id="eliminar-id">
</form>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Rellena el modal de editar y lo abre
    function abrirModalEditar(id, fecha, hora, estado) {
        document.getElementById('edit-id').value     = id;
        document.getElementById('edit-fecha').value  = fecha;
        document.getElementById('edit-hora').value   = hora;
        document.getElementById('edit-estado').value = estado;
        new bootstrap.Modal(document.getElementById('modalEditarCita')).show();
    }

    // Pide confirmación y, si el usuario acepta, envía el formulario de borrado
    function confirmarEliminar(id) {
        if (confirm('¿Seguro que quieres eliminar esta cita? Esta acción no se puede deshacer.')) {
            document.getElementById('eliminar-id').value = id;
            document.getElementById('form-eliminar').submit();
        }
    }
</script>
