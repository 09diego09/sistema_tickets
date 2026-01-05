</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div> <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

<script>
    particlesJS("particles-js", {
      "particles": {
        "number": {
          "value": 60, /* Un poco menos de cantidad para que sea más elegante */
          "density": { "enable": true, "value_area": 800 }
        },
        "color": {
          "value": "#0072ff" 
        },
        "shape": {
          "type": "circle",
          "stroke": { "width": 0, "color": "#000000" }
        },
        "opacity": {
          "value": 0.5,
          "random": false
        },
        "size": {
          "value": 3,
          "random": true
        },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#0072ff", 
          "opacity": 0.3,     
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 1.5, /* Movimiento suave */
          "direction": "none",
          "random": false,
          "straight": false,
          "out_mode": "out",
          "bounce": false
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": { "enable": true, "mode": "grab" },
          "onclick": { "enable": true, "mode": "push" },
          "resize": true
        },
        "modes": {
          "grab": { "distance": 140, "line_linked": { "opacity": 1 } }
        }
      },
      "retina_detect": true
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Buscamos todas las alertas en la página
        var alertas = document.querySelectorAll('.alert');
        
        // Si hay alertas, esperamos 2 segundos (2000 ms) y las cerramos
        if (alertas.length > 0) {
            setTimeout(function() {
                alertas.forEach(function(alerta) {
                    // Usamos la función nativa de Bootstrap para cerrar la alerta con animación
                    var bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                });
            }, 2000); // 2000 milisegundos = 2 segundos
        }
    });
</script>

<footer class="..."> ... </footer>

    <?php include 'chatbot.php'; ?>

    <script src="..."></script>
</body>
</html>

</body> </html>
</body>
</html>
</body>
</html>