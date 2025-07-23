<?php
// preloader.php
?>

<!-- Preloader -->
<style>
  #preloader {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    background-color: #fff; /* Fondo blanco sólido */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: opacity 1s ease;
  }

  .animation__shake {
    animation: shake 1s infinite;
  }

  @keyframes shake {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(3deg); }
    50% { transform: rotate(0deg); }
    75% { transform: rotate(-3deg); }
    100% { transform: rotate(0deg); }
  }
</style>

<div id="preloader">
  <img src="../CSS/CSSlogin/Imagenes/LogoCarWash2.PNG" alt="Logo Cargando" class="animation__shake" width="100" height="100">
</div>

<script>
window.addEventListener('load', function () {
  const preloader = document.getElementById('preloader');

  // Mantener preloader visible 1 segundo
  setTimeout(() => {
    preloader.style.opacity = '0'; // Inicia transición de opacidad

    setTimeout(() => {
      preloader.remove(); // Elimina preloader del DOM después de la transición
    }, 1000); // 1 segundo para la transición
  }, 500); // 1 segundo visible antes de empezar la transición
});
</script>
