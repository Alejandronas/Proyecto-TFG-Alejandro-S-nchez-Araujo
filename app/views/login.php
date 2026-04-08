<?php include_once('includes/header.php'); ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4">
            <div class="mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-hospital" style="font-size:1.4rem;color:var(--verde)"></i>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:18px">Acceso personal</h4>
                    <p class="text-muted mb-0" style="font-size:12px">Área restringida para empleados de la clínica</p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?vista=login">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#f9f9f8">
                            <i class="bi bi-person" style="color:var(--gris-medio)"></i>
                        </span>
                        <input type="text" name="username" class="form-control"
                               placeholder="pedro.alonso" autocomplete="username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#f9f9f8">
                            <i class="bi bi-lock" style="color:var(--gris-medio)"></i>
                        </span>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" autocomplete="current-password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                </button>
            </form>

            <hr class="my-3">
            <p class="text-muted text-center mb-0" style="font-size:12px">
                <i class="bi bi-shield-lock me-1"></i>
                médico · enfermera · recepcionista · administrador · laboratorio
            </p>
        </div>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
