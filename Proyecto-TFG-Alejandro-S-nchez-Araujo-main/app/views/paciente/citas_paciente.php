<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/CitaModel.php';

$modelo     = new CitaModel($pdo);
$todasCitas = $modelo->obtenerCitasPaciente($_SESSION['id_paciente']);

$filtroEstado = $_GET['estado'] ?? '';
$filtroDesde  = $_GET['desde']  ?? '';
$filtroHasta  = $_GET['hasta']  ?? '';

$citasFiltradas = array_filter($todasCitas, function($c) use ($filtroEstado, $filtroDesde, $filtroHasta) {
    if ($filtroEstado && $c['estado'] !== $filtroEstado) return false;
    if ($filtroDesde  && $c['fecha_cita'] < $filtroDesde)  return false;
    if ($filtroHasta  && $c['fecha_cita'] > $filtroHasta)  return false;
    return true;
});

$colores = ['#0a6e5c','#12907a','#2ec4a5','#c8903a','#d95f5f','#5a9e8f'];
$ok = $_GET['ok'] ?? '';
?>

<link rel="stylesheet" href="/assets/css/panel_medico.css">
<link rel="stylesheet" href="/assets/css/citas_medico.css">
<link rel="stylesheet" href="/assets/css/panel_paciente.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<?php
$pagina_activa_pac = 'citas';
require_once __DIR__ . '/../../includes/sidebar_paciente.php';
?>

<main class="panel-principal">

    <div class="cabecera">
        <h1>Mis Citas</h1>
        <div class="cabecera__fecha">
            <i class="bi bi-calendar3"></i>
            <span><?= date('d/m/Y') ?></span>
        </div>
    </div>

    <?php if ($ok): ?>
        <div class="conf-alerta conf-alerta--ok" style="background:#ddf6f1;color:#0a6e5c;border:1px solid #b2e8de;border-radius:12px;padding:12px 18px;font-size:.875rem;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-check-circle-fill"></i> Cita solicitada correctamente.
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <form method="GET" action="/citas_paciente.php">
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
            <button type="submit" class="btn-filtrar"><i class="bi bi-search"></i> Filtrar</button>
            <a href="/citas_paciente.php" class="btn-limpiar">Limpiar</a>
            <button type="button" class="btn-nueva-cita ms-auto" data-bs-toggle="modal" data-bs-target="#modalSolicitarCita">
                <i class="bi bi-plus-lg"></i> Solicitar Cita
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
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citasFiltradas as $i => $c):
                        $iniciales = strtoupper(substr($c['medico_nombre'],0,1) . substr($c['medico_apellido'],0,1));
                        $color     = $colores[$i % count($colores)];
                    ?>
                    <tr>
                        <td>
                            <div class="paciente-cell">
                                <div class="mini-avatar" style="background:<?= $color ?>"><?= $iniciales ?></div>
                                Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($c['especialidad'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($c['fecha_cita'])) ?></td>
                        <td><?= substr($c['hora_cita'],0,5) ?></td>
                        <td><span class="badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>


<!-- MODAL — SOLICITAR CITA -->
<div class="modal fade" id="modalSolicitarCita" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cita-modal">
            <div class="modal-header cita-modal__header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Solicitar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/controllers/PerfilPacienteController.php?accion=solicitar_cita">
                <div class="modal-body cita-modal__body">
                    <p class="text-muted" style="font-size:.85rem;margin-bottom:16px;">
                        <i class="bi bi-info-circle me-1"></i>
                        El médico será asignado por el personal de recepción.
                    </p>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Fecha</label>
                        <input type="date" name="fecha_cita" class="form-control cita-modal__input"
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label cita-modal__label">Hora</label>
                        <input type="time" name="hora_cita" class="form-control cita-modal__input" required>
                    </div>
                </div>
                <div class="modal-footer cita-modal__footer">
                    <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-guardar"><i class="bi bi-check-lg"></i> Solicitar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
