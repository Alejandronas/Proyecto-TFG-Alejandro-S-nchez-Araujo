<?php
// Clínica General — Pie de página compartido
?>

<?php if (!($is_homepage ?? false)): ?>
</div><!-- /.page-container -->
<?php endif; ?>
</main>

<footer>
  <span class="footer-brand">Clínica General</span>
  <span>© <?= date('Y') ?> · Alejandro Sánchez Araujo · 2º ASIR · clinicageneral.local</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
