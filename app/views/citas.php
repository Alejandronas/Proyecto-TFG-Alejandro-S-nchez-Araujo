<?php
$pre = $_POST + $_GET;
include_once('includes/header.php');
?>

<div class="row justify-content-center">
    <div class="col-md-7">

        <div class="page-header">
            <div>
                <h2><i class="bi bi-calendar-plus" style="color:var(--verde)"></i> Solicitar cita online</h2>
                <div class="subtitle">Rellena el formulario y te confirmaremos la cita por teléfono</div>
            </div>
        </div>

        <?php if (!empty($exito)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <strong>¡Cita confirmada!</strong> Recibirás confirmación por teléfono en breve.<br>
                    <a href="index.php" class="alert-link">← Volver al inicio</a>
                </div>
            </div>

        <?php else: ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body p-0">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--borde)">
                        <span class="section-title mb-0" style="border:none">
                            <i class="bi bi-person-fill me-1"></i>Datos de la cita
                        </span>
                    </div>
                    <div style="padding:24px">
                        <form method="POST" action="index.php?vista=citas">

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control"
                                           value="<?= htmlspecialchars($pre['nombre'] ?? '') ?>"
                                           placeholder="María" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" name="apellido" class="form-control"
                                           value="<?= htmlspecialchars($pre['apellido'] ?? '') ?>"
                                           placeholder="García López" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono de contacto</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f9f9f8">
                                        <i class="bi bi-telephone" style="color:var(--gris-medio)"></i>
                                    </span>
                                    <input type="tel" name="telefono" class="form-control"
                                           value="<?= htmlspecialchars($pre['telefono'] ?? '') ?>"
                                           placeholder="611 000 000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Especialista</label>
                                <select name="id_empleado" class="form-select" required>
                                    <option value="">— Selecciona especialista —</option>
                                    <?php foreach ($medicos as $m): ?>
                                        <option value="<?= $m->id ?>"
                                            <?= (($pre['id_empleado'] ?? '') == $m->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m->nombreCompleto()) ?>
                                            (<?= ucfirst($m->rol) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label">Fecha preferida</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#f9f9f8">
                                            <i class="bi bi-calendar3" style="color:var(--gris-medio)"></i>
                                        </span>
                                        <input type="date" name="fecha" class="form-control"
                                               value="<?= htmlspecialchars($pre['fecha'] ?? '') ?>"
                                               min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Hora</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#f9f9f8">
                                            <i class="bi bi-clock" style="color:var(--gris-medio)"></i>
                                        </span>
                                        <select name="hora" class="form-select" required>
                                            <option value="">— Seleccionar —</option>
                                            <?php
                                            $horas = ['08:00','08:30','09:00','09:30','10:00','10:30',
                                                      '11:00','11:30','12:00','16:00','16:30','17:00','17:30'];
                                            foreach ($horas as $h): ?>
                                                <option value="<?= $h ?>:00"
                                                    <?= (($pre['hora'] ?? '') === $h . ':00') ? 'selected' : '' ?>>
                                                    <?= $h ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100" style="padding:12px">
                                <i class="bi bi-calendar-check me-2"></i>Confirmar cita
                            </button>

                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
