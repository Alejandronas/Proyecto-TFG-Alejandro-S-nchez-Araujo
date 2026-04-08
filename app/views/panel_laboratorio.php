<?php include_once('includes/header.php'); ?>

<div class="page-header">
  <div>
    <h2><i class="bi bi-eyedropper" style="color:var(--verde)"></i> Panel Laboratorio</h2>
    <div class="subtitle"><i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['nombre']) ?></div>
  </div>
  <span style="font-size:12px;color:var(--gris-medio)"><?= date('d/m/Y') ?></span>
</div>

<?php if (!empty($msg_lab)): ?>
  <div class="alert alert-<?= $msg_lab === 'ok' ? 'success' : 'danger' ?> d-flex align-items-center gap-2">
    <i class="bi bi-<?= $msg_lab === 'ok' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= $msg_lab === 'ok' ? 'Resultado guardado correctamente.' : 'Error al guardar el resultado.' ?>
  </div>
<?php endif; ?>

<!-- REGISTRAR RESULTADO -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
      <i class="bi bi-plus-circle" style="color:var(--verde)"></i>
      <span class="section-title mb-0" style="border:none">Registrar resultado</span>
    </div>
    <div style="padding:20px">
      <form method="POST" action="index.php?vista=panel&accion=guardar_resultado">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Solicitud de análisis</label>
            <select name="id_solicitud" class="form-select form-select-sm" required>
              <option value="">— Seleccionar solicitud —</option>
              <?php foreach ($datos['solicitudes'] as $s): ?>
                <option value="<?= $s['id_solicitud'] ?>">
                  #<?= $s['id_solicitud'] ?> · <?= htmlspecialchars($s['nombre'] . ' ' . $s['apellido']) ?>
                  (<?= ucfirst($s['prioridad']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Tipo de prueba</label>
            <select name="id_prueba" class="form-select form-select-sm" required>
              <option value="">— Seleccionar prueba —</option>
              <?php foreach ($datos['tipos'] as $t): ?>
                <option value="<?= $t['id_prueba'] ?>">
                  <?= htmlspecialchars($t['nombre_prueba']) ?> (<?= $t['unidad_medida'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Valor obtenido</label>
            <input type="number" step="0.0001" name="valor" class="form-control form-control-sm"
                   placeholder="95.50" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Observaciones</label>
            <input type="text" name="observaciones" class="form-control form-control-sm"
                   placeholder="Dentro del rango normal…">
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-save me-1"></i>Guardar resultado
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SOLICITUDES -->
<div class="card">
  <div class="card-body p-0">
    <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <i class="bi bi-clipboard2-data" style="color:var(--verde)"></i>
        <span class="section-title mb-0" style="border:none">Solicitudes de análisis</span>
      </div>
      <span class="badge bg-secondary"><?= count($datos['solicitudes']) ?> solicitudes</span>
    </div>

    <?php if (empty($datos['solicitudes'])): ?>
      <p class="text-muted p-4 mb-0" style="font-size:13px">
        <i class="bi bi-inbox me-1"></i>No hay solicitudes en el sistema.
      </p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>#</th><th>Fecha</th><th>Paciente</th><th>Motivo</th><th>Prioridad</th><th>Resultados</th></tr>
          </thead>
          <tbody>
            <?php foreach ($datos['solicitudes'] as $s): ?>
              <tr>
                <td class="text-muted"><?= $s['id_solicitud'] ?></td>
                <td><?= date('d/m/Y', strtotime($s['fecha_solicitud'])) ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($s['nombre'] . ' ' . $s['apellido']) ?></td>
                <td class="text-muted" style="font-size:12.5px">
                  <?= htmlspecialchars(mb_substr($s['motivo_consulta'] ?? '—', 0, 40)) ?>…
                </td>
                <td>
                  <span class="badge bg-<?= $s['prioridad'] === 'urgente' ? 'danger' : 'info' ?>">
                    <i class="bi bi-<?= $s['prioridad'] === 'urgente' ? 'alarm' : 'hourglass-split' ?> me-1"></i>
                    <?= ucfirst($s['prioridad']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($s['num_resultados'] > 0): ?>
                    <span class="badge bg-success"><i class="bi bi-check me-1"></i><?= $s['num_resultados'] ?></span>
                  <?php else: ?>
                    <span class="badge bg-light text-dark border">Pendiente</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>
