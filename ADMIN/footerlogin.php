<!-- =========================
     PIE DE PÁGINA PRINCIPAL
     ========================= -->
<footer class="main-footer text-center text-sm-start">
  <!-- Mostrar el año actual automáticamente y nombre del sistema -->
  <strong>&copy; <?php echo date('Y'); ?> <a href="#">Debug Car Wash</a>.</strong>
  Todos los derechos reservados.

  <!-- Mostrar la versión del sistema, solo en pantallas grandes -->
  <div class="float-right d-none d-sm-inline-block">
    <b>Versión</b> 1.0.0
  </div>
</footer>

<!-- =========================
     CIERRE DEL CONTENEDOR PRINCIPAL
     ========================= -->
</div> <!-- Fin de <div class="wrapper"> -->

<!-- =========================
     SCRIPTS MÍNIMOS NECESARIOS
     ========================= -->

<!-- jQuery: base para muchas funciones JS, requerido por Bootstrap y DataTables -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap: para estilos dinámicos y componentes responsive -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- =========================
     DATATABLES: TABLAS AVANZADAS
     ========================= -->

<!-- DataTables principal -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>

<!-- Estilos integrados con Bootstrap 4 -->
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- =========================
     CONFIGURACIÓN DE LAS TABLAS
     ========================= -->
<script>
  $(function () {
    // Tabla con ID 'example1' — con botones de exportación y responsive
    $("#example1").DataTable({
      "responsive": true,         // Adaptación a diferentes resoluciones
      "lengthChange": false,      // Oculta el selector de cantidad de registros por página
      "autoWidth": false,         // Desactiva el cálculo automático de ancho
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"] // Botones de exportación y visibilidad de columnas
    })
    .buttons().container()
    .appendTo('#example1_wrapper .col-md-6:eq(0)'); // Coloca los botones en la parte superior derecha

    // Segunda tabla, configuración básica
    $('#example2').DataTable({
      "paging": true,             // Paginación activa
      "lengthChange": false,      // Desactiva cambio de cantidad de filas visibles
      "searching": false,         // Desactiva barra de búsqueda
      "ordering": true,           // Ordenamiento por columnas
      "info": true,               // Muestra información del número de registros
      "autoWidth": false,         // Ancho manual
      "responsive": true          // Adaptación responsiva
    });
  });
</script>

<!-- =========================
     SCRIPT PERSONALIZADO
     ========================= -->
<!-- Este archivo puede contener funciones adicionales como chat, notificaciones, etc. -->
<script src="chat.js"></script>

<!-- =========================
     CIERRE DE DOCUMENTO
     ========================= -->
</body>
</html>
