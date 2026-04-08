<?php include_once('includes/header.php'); ?>

<div class="page-header">
  <div>
    <h2><i class="bi bi-bandaid" style="color:var(--verde)"></i> Panel Enfermería</h2>
    <div class="subtitle"><i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['nombre']) ?></div>
  </div>
  <span style="font-size:12px;color:var(--gris-medio)"><?= date('d/m/Y') ?></span>
</div>

<!-- STATS RÁPIDAS -->
<div class="row g-0 mb-4" style="border:1px solid var(--borde)">
  <div class="col-sm-6 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= count($datos['citas_hoy']) ?></div>
        <div class="stat-label">Citas hoy</div>
      </div>
      <i class="bi bi-calendar2-day stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-6 stat-card border-0">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= count($datos['alergicos']) ?></div>
        <div class="stat-label">Pacientes con alergias</div>
      </div>
      <i class="bi bi-exclamation-triangle stat-icon" style="color:#ffc107"></i>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- CITAS DE HOY -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-body p-0">
        <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
          <i class="bi bi-clock-history" style="color:var(--verde)"></i>
          <span class="section-title mb-0" style="border:none">Citas de hoy</span>
        </div>
        <?php if (empty($datos['citas_hoy'])): ?>
          <p class="text-muted p-4 mb-0" style="font-size:13px">
            <i class="bi bi-calendar-check me-1"></i>No hay citas programadas para hoy.
          </p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Hora</th><th>Paciente</th><th>Teléfono</th><th>Especialista</th><th>Estado</th></tr>
              </thead>
              <tbody>
                <?php foreach ($datos['citas_hoy'] as $c): ?>
                  <tr>
                    <td class="fw-semibold"><?= substr($c['hora_cita'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($c['nom_p'] . ' ' . $c['ape_p']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['nom_e'] . ' ' . $c['ape_e']) ?></td>
                    <td>
                      <span class="badge bg-<?= $c['estado'] === 'programada' ? 'primary' : ($c['estado'] === 'completada' ? 'success' : 'danger') ?>">
                        <?= ucfirst($c['estado']) ?>
                      </span>
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

  <!-- ALERGIAS -->
  <div class="col-lg-5">
    <div class="card h-100" style="border-top:3px solid #e53e3e">
      <div class="card-body p-0">
        <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
          <i class="bi bi-exclamation-triangle-fill" style="color:#e53e3e"></i>
          <span class="section-title mb-0" style="border:none">Alergias conocidas</span>
        </div>
        <?php if (empty($datos['alergicos'])): ?>
          <p class="text-muted p-4 mb-0" style="font-size:13px">Sin registros de alergias.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Paciente</th><th>Alergias</th></tr>
              </thead>
              <tbody>
                <?php foreach ($datos['alergicos'] as $a): ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?></td>
                    <td>
                      <span class="badge bg-danger" style="font-size:11px">
                        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($a['alergias']) ?>
                      </span>
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

</div>

<?php include_once('includes/footer.php'); ?>
