<?php
$titulo_pagina = 'Inicio';
$is_homepage   = true;
include_once('includes/header.php');
?>

<style>
  :root {
    --verde:       #0a6e5c;
    --verde-claro: #12907a;
    --verde-suave: #e8f5f2;
    --crema:       #f9f6f1;
    --gris-texto:  #2c2c2c;
    --gris-suave:  #6b6b6b;
    --borde:       #dde8e5;
  }

  .hero { min-height: calc(100vh - 72px); }
  .hero-left {
    background: var(--crema);
    padding: 80px 64px;
    display: flex; flex-direction: column; justify-content: center;
    animation: fadeUp 0.9s ease both;
  }
  .hero-tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--verde-suave); color: var(--verde);
    font-size: 12px; font-weight: 500; letter-spacing: 1px;
    text-transform: uppercase; padding: 6px 14px; border-radius: 20px;
    margin-bottom: 28px; width: fit-content;
  }
  .hero-tag::before { content:''; width:6px; height:6px; background:var(--verde); border-radius:50%; }
  .hero-left h1 { font-size: clamp(40px,5vw,60px); font-weight:700; line-height:1.1; letter-spacing:-1px; margin-bottom:24px; }
  .hero-left h1 em { color: var(--verde); font-style: italic; }
  .hero-desc { font-size:17px; color:var(--gris-suave); line-height:1.7; font-weight:300; margin-bottom:40px; }
  .hero-stats { border-top:1px solid var(--borde); padding-top:36px; margin-top:48px; }
  .stat-num { font-family:'Playfair Display',serif; font-size:32px; font-weight:700; color:var(--verde); }
  .stat-label { font-size:13px; color:var(--gris-suave); }
  .hero-right {
    background: var(--verde);
    display:flex; align-items:center; justify-content:center;
    padding:80px 48px; position:relative; overflow:hidden;
    animation: fadeIn 1.1s ease both;
    min-height: calc(100vh - 72px);
  }
  .hero-right::before {
    content:''; position:absolute; top:-100px; right:-100px;
    width:400px; height:400px; border-radius:50%;
    background:rgba(255,255,255,0.04); pointer-events:none;
  }
  .cita-card {
    background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2);
    border-radius:16px; padding:36px; width:100%; max-width:380px; position:relative; z-index:1;
  }
  .cita-card h3 { font-family:'Playfair Display',serif; font-size:22px; color:#fff; margin-bottom:4px; }
  .cita-card .sub { font-size:13px; color:rgba(255,255,255,0.6); margin-bottom:24px; }
  .cita-card .form-label { font-size:11px; color:rgba(255,255,255,0.7); letter-spacing:.8px; text-transform:uppercase; }
  .cita-card .form-control, .cita-card .form-select {
    background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);
    border-radius:8px; color:#fff; font-size:14px;
  }
  .cita-card .form-control::placeholder { color:rgba(255,255,255,0.4); }
  .cita-card .form-control:focus, .cita-card .form-select:focus {
    background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.5); color:#fff; box-shadow:none;
  }
  .cita-card .form-select option { color:var(--gris-texto); background:#fff; }
  .btn-cita-form {
    width:100%; padding:13px; background:#fff; color:var(--verde);
    border:none; border-radius:8px; font-size:15px; font-weight:500;
    transition:opacity .2s, transform .2s; cursor:pointer;
  }
  .btn-cita-form:hover { opacity:.92; transform:translateY(-1px); }

  .servicios { background:#fff; padding:100px 0; }
  .section-tag { font-size:12px; font-weight:500; letter-spacing:2px; text-transform:uppercase; color:var(--verde); margin-bottom:12px; }
  .section-title { font-family:'Playfair Display',serif; font-size:clamp(30px,4vw,44px); font-weight:700; letter-spacing:-.5px; line-height:1.15; }
  .section-desc { font-size:16px; color:var(--gris-suave); font-weight:300; line-height:1.7; }
  .servicio-card { border:1px solid var(--borde); border-radius:12px; padding:32px 24px; height:100%; transition:border-color .3s, transform .3s, box-shadow .3s; }
  .servicio-card:hover { border-color:var(--verde); transform:translateY(-4px); box-shadow:0 12px 40px rgba(10,110,92,.1); }
  .servicio-icon { width:48px; height:48px; background:var(--verde-suave); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:18px; }
  .servicio-card h3 { font-family:'Playfair Display',serif; font-size:18px; margin-bottom:10px; }
  .servicio-card p { font-size:14px; color:var(--gris-suave); line-height:1.6; font-weight:300; margin:0; }

  .departamentos { background:var(--crema); padding:100px 0; }
  .dept-item { display:flex; align-items:center; gap:12px; padding:18px 20px; background:#fff; border-radius:10px; border:1px solid var(--borde); transition:border-color .2s; margin-bottom:12px; }
  .dept-item:hover { border-color:var(--verde); }
  .dept-dot { width:10px; height:10px; background:var(--verde); border-radius:50%; flex-shrink:0; }
  .dept-item span { font-size:14px; font-weight:500; }

  .contacto-strip { background:var(--verde); padding:72px 0; }
  .contacto-strip h2 { font-family:'Playfair Display',serif; font-size:clamp(26px,3vw,36px); color:#fff; line-height:1.2; }
  .contacto-strip h2 em { opacity:.7; font-style:italic; }
  .contacto-dato .label { font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:4px; }
  .contacto-dato .valor { font-size:15px; color:#fff; font-weight:500; }

  @keyframes fadeUp  { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
  @keyframes fadeIn  { from{opacity:0} to{opacity:1} }
  .reveal { opacity:0; transform:translateY(24px); transition:opacity .7s ease, transform .7s ease; }
  .reveal.visible { opacity:1; transform:translateY(0); }
</style>

<!-- HERO -->
<section class="hero">
  <div class="row g-0" style="min-height:calc(100vh - 72px);">
    <div class="col-lg-6 hero-left">
      <div class="hero-tag">Clínica General · Madrid</div>
      <h1>Tu salud,<br>nuestra <em>prioridad</em></h1>
      <p class="hero-desc">Atención médica integral para toda la familia. Especialistas, enfermería, laboratorio y administración reunidos en un mismo centro.</p>
      <div class="d-flex gap-3 flex-wrap">
        <a href="index.php?vista=citas"  class="btn btn-nav-cita px-4 py-2">Solicitar cita</a>
        <a href="#servicios"             class="btn btn-nav-login px-4 py-2">Ver servicios</a>
      </div>
      <div class="hero-stats row g-0">
        <div class="col-4"><div class="stat-num">8</div><div class="stat-label">Departamentos</div></div>
        <div class="col-4"><div class="stat-num">24h</div><div class="stat-label">Servicio continuo</div></div>
        <div class="col-4"><div class="stat-num">+500</div><div class="stat-label">Pacientes atendidos</div></div>
      </div>
    </div>
    <div class="col-lg-6 hero-right">
      <form class="cita-card" method="GET" action="index.php">
        <input type="hidden" name="vista" value="citas">
        <h3>Pedir cita online</h3>
        <p class="sub">Reserva tu consulta en menos de un minuto</p>
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" placeholder="María">
        </div>
        <div class="mb-3">
          <label class="form-label">Apellidos</label>
          <input type="text" name="apellido" class="form-control" placeholder="García López">
        </div>
        <div class="mb-3">
          <label class="form-label">Teléfono</label>
          <input type="tel" name="telefono" class="form-control" placeholder="611 000 000">
        </div>
        <div class="mb-3">
          <label class="form-label">Fecha preferida</label>
          <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn-cita-form mt-1">
          Confirmar cita →
        </button>
      </form>
    </div>
  </div>
</section>

<!-- SERVICIOS -->
<section class="servicios" id="servicios">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-tag">Lo que ofrecemos</div>
      <h2 class="section-title">Servicios médicos</h2>
      <p class="section-desc mx-auto mt-3" style="max-width:520px">Contamos con todas las especialidades para cubrir las necesidades de salud de nuestros pacientes.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3 reveal"><div class="servicio-card"><div class="servicio-icon">🩺</div><h3>Consultas médicas</h3><p>Atención por especialistas en cardiología, dermatología, traumatología y medicina general.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal"><div class="servicio-card"><div class="servicio-icon">🧪</div><h3>Laboratorio</h3><p>Análisis clínicos completos con resultados rápidos. Hemograma, glucosa, colesterol y más.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal"><div class="servicio-card"><div class="servicio-icon">💊</div><h3>Recetas y tratamientos</h3><p>Gestión digital de recetas y seguimiento de tratamientos integrado en el historial clínico.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal"><div class="servicio-card"><div class="servicio-icon">📋</div><h3>Historial clínico</h3><p>Historial médico completo y accesible para cada paciente, con antecedentes y alergias.</p></div></div>
    </div>
  </div>
</section>

<!-- DEPARTAMENTOS -->
<section class="departamentos" id="departamentos">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-tag">Nuestra estructura</div>
      <h2 class="section-title">Departamentos</h2>
      <p class="section-desc mx-auto mt-3" style="max-width:520px">Organización interna diseñada para ofrecer la mejor atención a cada paciente.</p>
    </div>
    <div class="row">
      <?php foreach (['Dirección','Administración','Recursos Humanos','Informática / IT','Especialistas','Enfermería','Recepción','Laboratorio'] as $dep): ?>
        <div class="col-md-6 col-lg-3 reveal">
          <div class="dept-item"><div class="dept-dot"></div><span><?= $dep ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CONTACTO -->
<section class="contacto-strip" id="contacto">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <h2>¿Necesitas <em>atención médica</em>?<br>Estamos aquí para ayudarte.</h2>
      </div>
      <div class="col-lg-6">
        <div class="row text-center g-3">
          <div class="col-4 contacto-dato"><div class="label">Teléfono</div><div class="valor">900 000 001</div></div>
          <div class="col-4 contacto-dato"><div class="label">Email</div><div class="valor" style="font-size:12px">info@clinicageneral.local</div></div>
          <div class="col-4 contacto-dato"><div class="label">Horario</div><div class="valor">Lun–Vie 8–20h</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e,i) => { if(e.isIntersecting) setTimeout(()=>e.target.classList.add('visible'), i*80); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
</script>

<?php include_once('includes/footer.php'); ?>
