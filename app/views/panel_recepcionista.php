<?php include_once('includes/header.php'); ?>

<div class="page-header">
  <div>
    <h2><i class="bi bi-telephone-inbound" style="color:var(--verde)"></i> Panel de Recepción</h2>
    <div class="subtitle"><i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['nombre']) ?></div>
  </div>
  <span style="font-size:12px;color:var(--gris-medio)"><?= date('d/m/Y') ?></span>
</div>

<?php if (!empty($msg)): ?>
  <div class="alert alert-<?= $msg === 'creada' ? 'success' : ($msg === 'cancelada' ? 'info' : 'danger') ?> d-flex align-items-center gap-2">
    <i class="bi bi-<?= $msg === 'creada' ? 'check-circle' : ($msg === 'cancelada' ? 'info-circle' : 'exclamation-triangle') ?>-fill"></i>
    <?= $msg === 'creada' ? 'Cita creada correctamente.' : ($msg === 'cancelada' ? 'Cita cancelada.' : 'Horario ocupado o datos incorrectos.') ?>
  </div>
<?php endif; ?>

<!-- NUEVA CITA -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:16px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
      <i class="bi bi-calendar-plus" style="color:var(--verde);font-size:1rem"></i>
      <span class="section-title mb-0" style="border:none">Nueva cita</span>
    </div>
    <div style="padding:20px">
      <form method="POST" action="index.php?vista=panel&accion=nueva_cita">
        <div class="row g-2 align-items-end">
          <div class="col-sm-2">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control form-control-sm" placeholder="María" required>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellido" class="form-control form-control-sm" placeholder="García López" required>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Teléfono</label>
            <input type="tel" name="telefono" class="form-control form-control-sm" placeholder="611000000" required>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Especialista</label>
            <select name="id_empleado" class="form-select form-select-sm" required>
              <option value="">— Seleccionar —</option>
              <?php foreach ($datos['medicos'] as $m): ?>
                <option value="<?= $m->id ?>"><?= htmlspecialchars($m->nombreCompleto()) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm"
                   min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-sm-1">
            <label class="form-label">Hora</label>
            <select name="hora" class="form-select form-select-sm" required>
              <option value="">—</option>
              <?php foreach (['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','16:00','16:30','17:00','17:30'] as $h): ?>
                <option value="<?= $h ?>:00"><?= $h ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Guardar cita
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- FILTRO + LISTADO -->
<div class="card">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
      <span class="section-title mb-0" style="border:none">
        <i class="bi bi-list-ul me-1"></i>Citas — <?= date('d/m/Y', strtotime($datos['fecha'])) ?>
      </span>
      <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
        <input type="hidden" name="vista" value="panel">
        <input type="date" name="fecha" class="form-control form-control-sm"
               value="<?= htmlspecialchars($datos['fecha']) ?>" style="width:160px">
        <button type="submit" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-funnel me-1"></i>Filtrar
        </button>
      </form>
    </div>

    <?php if (empty($datos['citas'])): ?>
      <p class="text-muted p-4 mb-0" style="font-size:13px">
        <i class="bi bi-calendar-x me-1"></i>No hay citas para esta fecha.
      </p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>Hora</th><th>Paciente</th><th>Teléfono</th><th>Especialista</th><th>Estado</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($datos['citas'] as $c): ?>
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
                <td>
                  <?php if ($c['estado'] === 'programada'): ?>
                    <form method="POST" action="index.php?vista=panel&accion=cancelar"
                          onsubmit="return confirm('¿Cancelar esta cita?')">
                      <input type="hidden" name="id_cita" value="<?= $c['id_cita'] ?>">
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                      </button>
                    </form>
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
