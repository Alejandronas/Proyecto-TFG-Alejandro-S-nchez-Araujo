<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/PacienteModel.php';

$modelo   = new PacienteModel($pdo);
$historial = $modelo->obtenerTodoElHistorial($_SESSION['id_empleado']);

$filtroDesde  = $_GET['desde']  ?? '';
$filtroHasta  = $_GET['hasta']  ?? '';
$filtroTipo   = $_GET['tipo']   ?? '';
$filtroBuscar = $_GET['buscar'] ?? '';

$filtrado = array_filter($historial, function($h) use ($filtroDesde, $filtroHasta, $filtroTipo, $filtroBuscar) {
    if ($filtroDesde  && $h['fecha'] < $filtroDesde) return false;
    if ($filtroHasta  && $h['fecha'] > $filtroHasta) return false;
    if ($filtroTipo   && $h['tipo_consulta'] !== $filtroTipo) return false;
    if ($filtroBuscar && stripos($h['diagnostico'] . ' ' . $h['paciente_nombre'] . ' ' . $h['paciente_apellido'], $filtroBuscar) === false) return false;
    return true;
});

$colores = ['#0a6e5c','#12907a','#2ec4a5','#c8903a','#d95f5f','#5a9e8f'];
?>

<link rel="stylesheet" href="/assets/css/panel_medico.css">
<link rel="stylesheet" href="/assets/css/citas_medico.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<?php
$pagina_activa = 'historial';
require_once __DIR__ . '/../../includes/sidebar_medico.php';
?>

<main class="panel-principal">

    <div class="cabecera">
        <h1>Historial Clínico</h1>
        <div class="cabecera__fecha">
            <i class="bi bi-calendar3"></i>
            <span><?= date('d/m/Y') ?></span>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="/historial.php">
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
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    <option value="presencial"   <?= $filtroTipo === 'presencial'   ? 'selected' : '' ?>>Presencial</option>
                    <option value="urgencia"     <?= $filtroTipo === 'urgencia'     ? 'selected' : '' ?>>Urgencia</option>
                    <option value="teleconsulta" <?= $filtroTipo === 'teleconsulta' ? 'selected' : '' ?>>Teleconsulta</option>
                </select>
            </div>
            <div>
                <label>Buscar</label>
                <input type="text" name="buscar" value="<?= htmlspecialchars($filtroBuscar) ?>"
                       placeholder="Paciente o diagnóstico…" style="min-width:200px">
            </div>
            <button type="submit" class="btn-filtrar">
                <i class="bi bi-search"></i> Filtrar
            </button>
            <a href="/historial.php" class="btn-limpiar">Limpiar</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="tabla-card">
        <h5>Total: <?= count($filtrado) ?> entradas</h5>

        <?php if (empty($filtrado)): ?>
            <div class="sin-resultados">
                <i class="bi bi-clipboard2-x"></i>
                No se encontraron entradas con los filtros aplicados.
            </div>
        <?php else: ?>
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filtrado as $i => $h):
                        $iniciales = strtoupper(substr($h['paciente_nombre'],0,1) . substr($h['paciente_apellido'],0,1));
                        $color     = $colores[$i % count($colores)];
                        $badgeTipo = match($h['tipo_consulta']) {
                            'urgencia'     => 'badge-cancelada',
                            'teleconsulta' => 'badge-completada',
                            default        => 'badge-programada'
                        };
                    ?>
                    <tr>
                        <td>
                            <div class="paciente-cell">
                                <div class="mini-avatar" style="background:<?= $color ?>"><?= $iniciales ?></div>
                                <?= htmlspecialchars($h['paciente_nombre'] . ' ' . $h['paciente_apellido']) ?>
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                        <td><span class="<?= $badgeTipo ?>"><?= ucfirst($h['tipo_consulta']) ?></span></td>
                        <td><?= htmlspecialchars($h['motivo_consulta'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['diagnostico']) ?></td>
                        <td><?= htmlspecialchars($h['tratamiento'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>
