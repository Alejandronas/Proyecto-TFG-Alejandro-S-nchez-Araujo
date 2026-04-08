<?php include_once('includes/header.php'); ?>

<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h2><i class="bi bi-heart-pulse" style="color:var(--verde)"></i> Panel Médico</h2>
    <div class="subtitle"><i class="bi bi-person me-1"></i>Dr/a. <?= htmlspecialchars($_SESSION['nombre']) ?></div>
  </div>
  <span style="font-size:12px;color:var(--gris-medio)"><?= date('l, d \d\e F \d\e Y') ?></span>
</div>

<!-- ESTADÍSTICAS -->
<?php
  $citas_hoy   = array_filter($datos['citas'], fn($c) => $c['fecha_cita'] === date('Y-m-d'));
  $citas_total = count($datos['citas']);
  $cons_total  = count($datos['consultas']);
?>
<div class="row g-0 mb-4" style="border:1px solid var(--borde)">
  <div class="col-sm-4 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= $citas_total ?></div>
        <div class="stat-label">Próximas citas</div>
      </div>
      <i class="bi bi-calendar2-week stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-4 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= count($citas_hoy) ?></div>
        <div class="stat-label">Citas hoy</div>
      </div>
      <i class="bi bi-clock stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-4 stat-card border-0">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= $cons_total ?></div>
        <div class="stat-label">Últimas consultas</div>
      </div>
      <i class="bi bi-journal-medical stat-icon"></i>
    </div>
  </div>
</div>

<!-- BUSCADOR DE PACIENTES -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:16px 20px;border-bottom:1px solid var(--borde)">
      <span class="section-title mb-0" style="border:none">
        <i class="bi bi-search me-1"></i>Buscar paciente
      </span>
    </div>
    <div style="padding:16px 20px">
      <form method="GET" action="index.php" class="d-flex gap-2">
        <input type="hidden" name="vista" value="panel">
        <input type="text" name="buscar" class="form-control"
               placeholder="Nombre, apellido o teléfono…"
               value="<?= htmlspecialchars($datos['buscar']) ?>" style="max-width:360px">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-search me-1"></i>Buscar
        </button>
        <?php if ($datos['buscar']): ?>
          <a href="index.php?vista=panel" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i>
          </a>
        <?php endif; ?>
      </form>

      <?php if ($datos['buscar'] && empty($datos['resultado_buscar'])): ?>
        <p class="text-muted mt-3 mb-0" style="font-size:13px">
          <i class="bi bi-info-circle me-1"></i>Sin resultados para «<?= htmlspecialchars($datos['buscar']) ?>».
        </p>
      <?php elseif (!empty($datos['resultado_buscar'])): ?>
        <div class="table-responsive mt-3">
          <table class="table mb-0">
            <thead>
              <tr><th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>F. nacimiento</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($datos['resultado_buscar'] as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['nombre']) ?></td>
                  <td><?= htmlspecialchars($p['apellido']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($p['telefono'] ?? '—') ?></td>
                  <td class="text-muted"><?= $p['fecha_nacimiento'] ? date('d/m/Y', strtotime($p['fecha_nacimiento'])) : '—' ?></td>
                  <td>
                    <a href="index.php?vista=historial&id_paciente=<?= $p['id_paciente'] ?>"
                       class="btn btn-sm btn-outline-success">
                      <i class="bi bi-folder2-open me-1"></i>Ver historial
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- PRÓXIMAS CITAS -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-body p-0">
        <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;justify-content:space-between;align-items:center">
          <span class="section-title mb-0" style="border:none">
            <i class="bi bi-calendar2-check me-1"></i>Próximas citas
          </span>
          <a href="index.php?vista=panel" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
          </a>
        </div>
        <?php if (empty($datos['citas'])): ?>
          <p class="text-muted p-4 mb-0" style="font-size:13px">
            <i class="bi bi-calendar-x me-1"></i>No hay citas próximas.
          </p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Estado</th><th></th></tr>
              </thead>
              <tbody>
                <?php foreach ($datos['citas'] as $c): ?>
                  <tr>
                    <td>
                      <?php
                        $hoy     = date('Y-m-d');
                        $manana  = date('Y-m-d', strtotime('+1 day'));
                        if ($c['fecha_cita'] === $hoy):
                      ?>
                        <span class="badge bg-success">Hoy</span>
                      <?php elseif ($c['fecha_cita'] === $manana): ?>
                        <span class="badge bg-warning text-dark">Mañana</span>
                      <?php else: ?>
                        <?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                      <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= substr($c['hora_cita'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($c['nom_p'] . ' ' . $c['ape_p']) ?></td>
                    <td>
                      <span class="badge bg-<?= $c['estado'] === 'programada' ? 'primary' : ($c['estado'] === 'completada' ? 'success' : 'danger') ?>">
                        <?= ucfirst($c['estado']) ?>
                      </span>
                    </td>
                    <td>
                      <a href="index.php?vista=historial&id_cita=<?= $c['id_cita'] ?>"
                         class="btn btn-sm btn-outline-success">
                        <i class="bi bi-clipboard2-pulse"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ÚLTIMAS CONSULTAS -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body p-0">
        <div style="padding:16px 20px;border-bottom:1px solid var(--borde)">
          <span class="section-title mb-0" style="border:none">
            <i class="bi bi-journal-text me-1"></i>Últimas consultas
          </span>
        </div>
        <?php if (empty($datos['consultas'])): ?>
          <p class="text-muted p-4 mb-0" style="font-size:13px">Sin consultas registradas.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Fecha</th><th>Paciente</th><th>Diagnóstico</th></tr>
              </thead>
              <tbody>
                <?php foreach ($datos['consultas'] as $c): ?>
                  <tr>
                    <td style="white-space:nowrap"><?= date('d/m/Y', strtotime($c['fecha_consulta'])) ?></td>
                    <td><?= htmlspecialchars($c['nom_p'] . ' ' . $c['ape_p']) ?></td>
                    <td class="text-muted" style="font-size:12.5px"><?= htmlspecialchars(mb_substr($c['diagnostico'] ?? '—', 0, 35)) ?>…</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php include_once('includes/footer.php'); ?>
