<!-- =======================
     PIE DE PÁGINA PRINCIPAL
     ======================= -->

<footer class="main-footer text-center text-sm-start">
  <!-- Año actual y nombre del sistema -->
  <strong>&copy; <?php echo date('Y'); ?> <a href="index.php">Debug Car Wash</a>.</strong>
  Todos los derechos reservados.

  <!-- Versión del sistema, visible solo en pantallas grandes -->
  <div class="float-right d-none d-sm-inline-block">
    <b>Versión</b> 1.0.0
  </div>
</footer>

<!-- =======================
     Estilo personalizado
     ======================= -->
<link rel="stylesheet" href="../CSS/FooterCSS/Footer.css">

<!-- ================================
     SIDEBAR LATERAL DE CONTROL (vacío)
     ================================ -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Aquí puedes agregar accesos rápidos, configuraciones o widgets laterales -->
</aside>

</div> <!-- Cierre de wrapper general -->

<!-- =======================
     LIBRERÍAS Y PLUGINS JS
     ======================= -->

<!-- jQuery: librería base -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- jQuery UI: para widgets como datepicker, dialog, etc. -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Corrige conflicto entre jQuery UI tooltip y Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap 4: framework de interfaz -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js: para gráficas estadísticas -->
<script src="plugins/chart.js/Chart.min.js"></script>

<!-- Sparkline: gráficas pequeñas tipo línea -->
<script src="plugins/sparklines/sparkline.js"></script>

<!-- JQVMap: mapas interactivos -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

<!-- jQuery Knob: indicadores tipo perilla -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>

<!-- Moment.js y DateRangePicker: manejo de fechas -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>

<!-- Tempus Dominus: calendario y reloj Bootstrap -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Summernote: editor de texto enriquecido -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>

<!-- OverlayScrollbars: scroll personalizado -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- AdminLTE: script principal de la plantilla -->
<script src="dist/js/adminlte.js"></script>

<!-- Scripts opcionales de demo o efectos extra -->
<script src="dist/js/demo.js"></script>
<script src="dist/js/pages/dashboard.js"></script>

<!-- ======================
     DATATABLES Y EXPORTACIÓN
     ====================== -->

<!-- Librerías DataTables para tablas interactivas -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- Botones y exportaciones (CSV, Excel, PDF, impresión, columnas) -->
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- Repetido pero no crítico (adminlte.min.js y demo.js ya cargados) -->
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/demo.js"></script>

<!-- ====================
     INICIALIZACIÓN DE TABLAS
     ==================== -->

<script>
  $(function () {
    // Tabla con ID #example1 — activación de botones de exportación
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"] // Exportar y ocultar columnas
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    // Segunda tabla (#example2) con configuración más básica
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true
    });
  });
</script>

<!-- Script personalizado del sistema (como chat u otras funciones extra) -->
<script src="chat.js"></script>

</body>
</html>
