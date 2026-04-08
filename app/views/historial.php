<?php include_once('includes/header.php'); ?>

<div class="page-header">
  <div>
    <h2><i class="bi bi-folder2-open" style="color:var(--verde)"></i> Historial Clínico</h2>
    <div class="subtitle"><?= htmlspecialchars($paciente->nombreCompleto()) ?></div>
  </div>
  <a href="index.php?vista=panel" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>Volver al panel
  </a>
</div>

<?php if (!empty($msg_ok)): ?>
  <div class="alert alert-success d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>Consulta registrada correctamente.
  </div>
<?php endif; ?>
<?php if (!empty($msg_receta)): ?>
  <div class="alert alert-<?= $msg_receta === 'ok' ? 'success' : 'danger' ?> d-flex align-items-center gap-2">
    <i class="bi bi-<?= $msg_receta === 'ok' ? 'capsule' : 'exclamation-triangle' ?>"></i>
    <?= $msg_receta === 'ok' ? 'Receta guardada correctamente.' : 'Error al guardar la receta.' ?>
  </div>
<?php endif; ?>

<!-- DATOS DEL PACIENTE -->
<div class="card mb-4" style="border-top:3px solid var(--verde)">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
      <i class="bi bi-person-vcard" style="color:var(--verde)"></i>
      <span class="section-title mb-0" style="border:none">Datos del paciente</span>
    </div>
    <div class="row g-0" style="padding:0">
      <div class="col-sm-4" style="padding:16px 20px;border-right:1px solid var(--borde)">
        <div class="form-label">Nombre completo</div>
        <div class="fw-semibold"><?= htmlspecialchars($paciente->nombreCompleto()) ?></div>
      </div>
      <div class="col-sm-2" style="padding:16px 20px;border-right:1px solid var(--borde)">
        <div class="form-label">Nacimiento</div>
        <div class="fw-semibold"><?= $paciente->fecha_nacimiento ? date('d/m/Y', strtotime($paciente->fecha_nacimiento)) : '—' ?></div>
      </div>
      <div class="col-sm-2" style="padding:16px 20px;border-right:1px solid var(--borde)">
        <div class="form-label">Teléfono</div>
        <div class="fw-semibold"><?= htmlspecialchars($paciente->telefono ?? '—') ?></div>
      </div>
      <?php if ($historial): ?>
      <div class="col-sm-2" style="padding:16px 20px;border-right:1px solid var(--borde)">
        <div class="form-label">Antecedentes</div>
        <div style="font-size:13px"><?= htmlspecialchars($historial['antecedentes_familiares'] ?? '—') ?></div>
      </div>
      <div class="col-sm-2" style="padding:16px 20px;background:#fff5f5">
        <div class="form-label" style="color:#c53030">
          <i class="bi bi-exclamation-triangle-fill me-1" style="color:#e53e3e"></i>Alergias
        </div>
        <div class="fw-semibold" style="color:#c53030;font-size:13px"><?= htmlspecialchars($historial['alergias'] ?? '—') ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- NUEVA CONSULTA -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;gap:8px">
      <i class="bi bi-pencil-square" style="color:var(--verde)"></i>
      <span class="section-title mb-0" style="border:none">Nueva consulta</span>
    </div>
    <div style="padding:20px">
      <form method="POST" action="index.php?vista=historial&id_paciente=<?= $paciente->id ?>&id_cita=<?= $id_cita ?>">
        <input type="hidden" name="accion" value="guardar_consulta">
        <div class="row g-3 mb-3">
          <div class="col-sm-4">
            <label class="form-label">Motivo de consulta</label>
            <input type="text" name="motivo" class="form-control" placeholder="Ej: Dolor abdominal" required>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Diagnóstico</label>
            <textarea name="diagnostico" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Tratamiento</label>
            <textarea name="tratamiento" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <!-- RECETA OPCIONAL -->
        <div style="background:#f9faf9;border:1px solid var(--borde);padding:16px;margin-bottom:16px">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--gris-medio)">
              <i class="bi bi-capsule me-1" style="color:var(--verde)"></i>Receta (opcional)
            </span>
            <button type="button" class="btn btn-outline-success btn-sm" id="btn-add-med">
              <i class="bi bi-plus-circle me-1"></i>Añadir medicamento
            </button>
          </div>
          <div id="med-rows"></div>
          <template id="tpl-med">
            <div class="row g-2 mb-2 med-row align-items-end">
              <div class="col-sm-3">
                <label class="form-label">Medicamento</label>
                <input type="text" name="medicamento[]" class="form-control form-control-sm" placeholder="Ibuprofeno" required>
              </div>
              <div class="col-sm-2">
                <label class="form-label">Dosis</label>
                <input type="text" name="dosis[]" class="form-control form-control-sm" placeholder="400mg">
              </div>
              <div class="col-sm-3">
                <label class="form-label">Frecuencia</label>
                <input type="text" name="frecuencia[]" class="form-control form-control-sm" placeholder="Cada 8h">
              </div>
              <div class="col-sm-3">
                <label class="form-label">Duración</label>
                <input type="text" name="duracion[]" class="form-control form-control-sm" placeholder="5 días">
              </div>
              <div class="col-sm-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 btn-del-med">
                  <i class="bi bi-trash3"></i>
                </button>
              </div>
            </div>
          </template>
        </div>

        <button type="submit" class="btn btn-success">
          <i class="bi bi-save me-1"></i>Guardar consulta
        </button>
      </form>
    </div>
  </div>
</div>

<!-- CONSULTAS ANTERIORES -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <i class="bi bi-journal-text" style="color:var(--verde)"></i>
        <span class="section-title mb-0" style="border:none">Consultas anteriores</span>
      </div>
      <span class="badge bg-secondary"><?= count($consultas) ?></span>
    </div>
    <?php if (empty($consultas)): ?>
      <p class="text-muted p-4 mb-0" style="font-size:13px">
        <i class="bi bi-inbox me-1"></i>Sin consultas registradas.
      </p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>Fecha</th><th>Médico</th><th>Motivo</th><th>Diagnóstico</th><th>Tratamiento</th></tr>
          </thead>
          <tbody>
            <?php foreach ($consultas as $c): ?>
              <tr>
                <td style="white-space:nowrap"><?= date('d/m/Y', strtotime($c['fecha_consulta'])) ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($c['nom_medico'] . ' ' . $c['ape_medico']) ?></td>
                <td><?= htmlspecialchars($c['motivo_consulta'] ?? '—') ?></td>
                <td class="text-muted" style="font-size:12.5px"><?= htmlspecialchars($c['diagnostico'] ?? '—') ?></td>
                <td class="text-muted" style="font-size:12.5px"><?= htmlspecialchars($c['tratamiento'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- RECETAS -->
<div class="card mb-4">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <i class="bi bi-capsule" style="color:var(--verde)"></i>
        <span class="section-title mb-0" style="border:none">Recetas emitidas</span>
      </div>
      <span class="badge bg-secondary"><?= count($recetas) ?></span>
    </div>
    <?php if (empty($recetas)): ?>
      <p class="text-muted p-4 mb-0" style="font-size:13px">
        <i class="bi bi-inbox me-1"></i>Sin recetas registradas.
      </p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>Fecha</th><th>Médico</th><th>Medicamentos</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recetas as $r): ?>
              <tr>
                <td style="white-space:nowrap"><?= date('d/m/Y', strtotime($r['fecha_emision'])) ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($r['nom_medico'] . ' ' . $r['ape_medico']) ?></td>
                <td style="font-size:12.5px"><?= htmlspecialchars($r['medicamentos'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- RESULTADOS DE LABORATORIO -->
<div class="card">
  <div class="card-body p-0">
    <div style="padding:14px 20px;border-bottom:1px solid var(--borde);display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:8px">
        <i class="bi bi-eyedropper" style="color:var(--verde)"></i>
        <span class="section-title mb-0" style="border:none">Resultados de laboratorio</span>
      </div>
      <span class="badge bg-secondary"><?= count($resultados_lab) ?></span>
    </div>
    <?php if (empty($resultados_lab)): ?>
      <p class="text-muted p-4 mb-0" style="font-size:13px">
        <i class="bi bi-inbox me-1"></i>Sin resultados de laboratorio registrados.
      </p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>Fecha</th><th>Prueba</th><th>Valor</th><th>Unidad</th><th>Observaciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($resultados_lab as $r): ?>
              <tr>
                <td style="white-space:nowrap">
                  <?= $r['fecha_procesamiento'] ? date('d/m/Y', strtotime($r['fecha_procesamiento'])) : date('d/m/Y', strtotime($r['fecha_solicitud'])) ?>
                </td>
                <td class="fw-semibold"><?= htmlspecialchars($r['nombre_prueba']) ?></td>
                <td class="fw-semibold" style="color:var(--verde)"><?= htmlspecialchars($r['valor_obtenido']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($r['unidad_medida'] ?? '') ?></td>
                <td class="text-muted" style="font-size:12.5px"><?= htmlspecialchars($r['observaciones'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('btn-add-med').addEventListener('click', function () {
  const tpl  = document.getElementById('tpl-med').content.cloneNode(true);
  const cont = document.getElementById('med-rows');
  cont.appendChild(tpl);
  cont.querySelectorAll('.btn-del-med').forEach(btn => {
    btn.onclick = () => btn.closest('.med-row').remove();
  });
});
</script>

<?php include_once('includes/footer.php'); ?>
