<?php include_once('includes/header.php'); ?>

<div class="page-header">
  <div>
    <h2><i class="bi bi-gear" style="color:var(--verde)"></i> Administración</h2>
    <div class="subtitle"><i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['nombre']) ?></div>
  </div>
  <span style="font-size:12px;color:var(--gris-medio)"><?= date('d/m/Y') ?></span>
</div>

<!-- ESTADÍSTICAS -->
<div class="row g-0 mb-4" style="border:1px solid var(--borde)">
  <div class="col-sm-3 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= $datos['stats']['empleados'] ?></div>
        <div class="stat-label">Empleados</div>
      </div>
      <i class="bi bi-people stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-3 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= $datos['stats']['pacientes'] ?></div>
        <div class="stat-label">Pacientes</div>
      </div>
      <i class="bi bi-person-heart stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-3 stat-card border-0 border-end">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num"><?= $datos['stats']['citas_hoy'] ?></div>
        <div class="stat-label">Citas hoy</div>
      </div>
      <i class="bi bi-calendar2-event stat-icon"></i>
    </div>
  </div>
  <div class="col-sm-3 stat-card border-0">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-num" style="color:#c05000"><?= $datos['stats']['pendientes'] ?></div>
        <div class="stat-label">Facturas pendientes</div>
      </div>
      <i class="bi bi-receipt stat-icon" style="color:#f5c084"></i>
    </div>
  </div>
</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-0" id="adminTabs">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="tab" href="#tab-empleados">
      <i class="bi bi-people me-1"></i>Empleados
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#tab-facturas">
      <i class="bi bi-receipt me-1"></i>Facturas
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#tab-usuarios">
      <i class="bi bi-shield-lock me-1"></i>Usuarios
    </a>
  </li>
</ul>

<div class="tab-content" style="border:1px solid var(--borde);border-top:none">

  <!-- ── TAB EMPLEADOS ─────────────────────── -->
  <div class="tab-pane fade show active" id="tab-empleados">

    <?php if (!empty($msg_emp)): ?>
      <div class="alert alert-<?= $msg_emp === 'ok' ? 'success' : 'danger' ?> d-flex align-items-center gap-2 m-3 mb-0">
        <i class="bi bi-<?= $msg_emp === 'ok' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= $msg_emp === 'ok' ? 'Operación realizada correctamente.' : 'Error al realizar la operación.' ?>
      </div>
    <?php endif; ?>

    <div style="padding:20px;border-bottom:1px solid var(--borde)">
      <div class="section-title"><i class="bi bi-person-plus me-1"></i>Añadir empleado</div>
      <form method="POST" action="index.php?vista=panel&accion=nuevo_empleado">
        <div class="row g-2 align-items-end">
          <div class="col-sm-2">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Juan" required>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellido" class="form-control form-control-sm" placeholder="García López" required>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select form-select-sm" required>
              <option value="">— Seleccionar —</option>
              <option value="medico">Médico</option>
              <option value="enfermera">Enfermera</option>
              <option value="recepcionista">Recepcionista</option>
              <option value="administrador">Administrador</option>
              <option value="laboratorio">Laboratorio</option>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Departamento</label>
            <select name="id_departamento" class="form-select form-select-sm" required>
              <option value="">— Seleccionar —</option>
              <?php foreach ($datos['departamentos'] as $d): ?>
                <option value="<?= $d['id_departamento'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Salario €</label>
            <input type="number" name="salario" class="form-control form-control-sm" placeholder="30000" step="100" min="0">
          </div>
          <div class="col-sm-2">
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="bi bi-person-plus me-1"></i>Añadir
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="section-title" style="padding:14px 20px 10px;margin:0;border-bottom:1px solid var(--borde)">
      <i class="bi bi-table me-1"></i>Plantilla actual
    </div>
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th>Nombre</th><th>Rol</th><th>Departamento</th><th>Salario</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($datos['empleados'] as $e): ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></td>
              <td>
                <span class="badge bg-secondary"><?= ucfirst($e['rol']) ?></span>
              </td>
              <td class="text-muted"><?= htmlspecialchars($e['departamento']) ?></td>
              <td><?= number_format($e['salario'], 2, ',', '.') ?> €</td>
              <td>
                <form method="POST" action="index.php?vista=panel&accion=eliminar_empleado"
                      onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars(addslashes($e['nombre'])) ?>?')">
                  <input type="hidden" name="id_empleado" value="<?= $e['id_empleado'] ?>">
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash3 me-1"></i>Eliminar
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── TAB FACTURAS ──────────────────────── -->
  <div class="tab-pane fade" id="tab-facturas">

    <?php if (!empty($msg_fac)): ?>
      <div class="alert alert-<?= $msg_fac === 'ok' ? 'success' : 'danger' ?> d-flex align-items-center gap-2 m-3 mb-0">
        <i class="bi bi-<?= $msg_fac === 'ok' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= $msg_fac === 'ok' ? 'Operación realizada correctamente.' : 'Error al procesar la factura.' ?>
      </div>
    <?php endif; ?>

    <div style="padding:20px;border-bottom:1px solid var(--borde)">
      <div class="section-title"><i class="bi bi-plus-circle me-1"></i>Nueva factura</div>
      <form method="POST" action="index.php?vista=panel&accion=nueva_factura">
        <div class="row g-2 align-items-end">
          <div class="col-sm-4">
            <label class="form-label">Paciente</label>
            <select name="id_paciente" class="form-select form-select-sm" required>
              <option value="">— Seleccionar paciente —</option>
              <?php foreach ($datos['pacientes'] as $p): ?>
                <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Total €</label>
            <input type="number" name="total" class="form-control form-control-sm" placeholder="45.00" step="0.01" min="0" required>
          </div>
          <div class="col-sm-2">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select form-select-sm">
              <option value="pendiente">Pendiente</option>
              <option value="pagada">Pagada</option>
            </select>
          </div>
          <div class="col-sm-2">
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="bi bi-receipt me-1"></i>Crear factura
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="section-title" style="padding:14px 20px 10px;margin:0;border-bottom:1px solid var(--borde)">
      <i class="bi bi-list-ul me-1"></i>Historial de facturas
    </div>
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th>#</th><th>Fecha</th><th>Paciente</th><th>Total</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($datos['facturas'] as $f): ?>
            <tr>
              <td class="text-muted"><?= $f['id_factura'] ?></td>
              <td><?= date('d/m/Y', strtotime($f['fecha'])) ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($f['nom_p'] . ' ' . $f['ape_p']) ?></td>
              <td class="fw-semibold"><?= number_format($f['total'], 2, ',', '.') ?> €</td>
              <td>
                <span class="badge bg-<?= $f['estado'] === 'pagada' ? 'success' : 'warning text-dark' ?>">
                  <i class="bi bi-<?= $f['estado'] === 'pagada' ? 'check-circle' : 'clock' ?> me-1"></i>
                  <?= ucfirst($f['estado']) ?>
                </span>
              </td>
              <td>
                <?php if ($f['estado'] === 'pendiente'): ?>
                  <form method="POST" action="index.php?vista=panel&accion=pagar_factura">
                    <input type="hidden" name="id_factura" value="<?= $f['id_factura'] ?>">
                    <button class="btn btn-sm btn-outline-success">
                      <i class="bi bi-check-lg me-1"></i>Marcar pagada
                    </button>
                  </form>
                <?php else: ?>
                  <span class="text-muted" style="font-size:12px"><i class="bi bi-check2-all"></i></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── TAB USUARIOS ──────────────────────── -->
  <div class="tab-pane fade" id="tab-usuarios">

    <?php if (!empty($msg_usr)): ?>
      <div class="alert alert-<?= $msg_usr === 'ok' ? 'success' : 'danger' ?> d-flex align-items-center gap-2 m-3 mb-0">
        <i class="bi bi-<?= $msg_usr === 'ok' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= $msg_usr === 'ok' ? 'Usuario creado correctamente.' : 'Error: ' . htmlspecialchars($msg_usr_detalle ?? 'no se pudo crear el usuario.') ?>
      </div>
    <?php endif; ?>

    <div style="padding:20px;border-bottom:1px solid var(--borde)">
      <div class="section-title"><i class="bi bi-person-plus me-1"></i>Crear acceso de usuario</div>
      <form method="POST" action="index.php?vista=panel&accion=nuevo_usuario">
        <div class="row g-2 align-items-end">
          <div class="col-sm-3">
            <label class="form-label">Empleado</label>
            <select name="id_empleado" class="form-select form-select-sm" required>
              <option value="">— Seleccionar empleado —</option>
              <?php foreach ($datos['empleados'] as $e): ?>
                <option value="<?= $e['id_empleado'] ?>">
                  <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?> (<?= ucfirst($e['rol']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Nombre de usuario</label>
            <input type="text" name="username" class="form-control form-control-sm"
                   placeholder="juan.garcia" autocomplete="off" required>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control form-control-sm"
                   placeholder="••••••••" autocomplete="new-password" required>
          </div>
          <div class="col-sm-2">
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="bi bi-person-check me-1"></i>Crear
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="section-title" style="padding:14px 20px 10px;margin:0;border-bottom:1px solid var(--borde)">
      <i class="bi bi-table me-1"></i>Usuarios del sistema
    </div>
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr><th>Empleado</th><th>Usuario</th><th>Rol</th><th></th></tr>
        </thead>
        <tbody>
          <?php if (empty($datos['usuarios'])): ?>
            <tr><td colspan="4" class="text-muted text-center p-4">
              <i class="bi bi-inbox me-1"></i>Sin usuarios registrados.
            </td></tr>
          <?php else: ?>
            <?php foreach ($datos['usuarios'] as $u): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
                <td>
                  <span style="font-family:monospace;font-size:13px;background:#f5f5f3;padding:2px 8px;border:1px solid var(--borde)">
                    <?= htmlspecialchars($u['username']) ?>
                  </span>
                </td>
                <td><span class="badge bg-secondary"><?= ucfirst($u['rol']) ?></span></td>
                <td>
                  <form method="POST" action="index.php?vista=panel&accion=eliminar_usuario"
                        onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars(addslashes($u['username'])) ?>?')">
                    <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash3 me-1"></i>Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include_once('includes/footer.php'); ?>
