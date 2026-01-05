<style>
    /* BOTÓN FLOTANTE */
    .chat-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: #0077c8;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 60px;
        font-size: 30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        cursor: pointer;
        z-index: 9999;
        transition: transform 0.3s;
    }
    .chat-btn:hover {
        transform: scale(1.1);
        background-color: #005c99;
    }

    /* VENTANA DEL CHAT */
    .chat-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        display: none; /* Oculto por defecto */
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e1e4e8;
    }

    /* ENCABEZADO */
    .chat-header {
        background: linear-gradient(135deg, #0077c8 0%, #005c99 100%);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* CUERPO DE MENSAJES */
    .chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f8f9fa;
        font-size: 0.9rem;
    }

    /* BURBUJAS DE MENSAJE */
    .msg {
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 15px;
        max-width: 80%;
        word-wrap: break-word;
        animation: fadeIn 0.3s ease;
    }
    .msg-bot {
        background-color: #e9ecef;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }
    .msg-user {
        background-color: #0077c8;
        color: white;
        align-self: flex-end;
        margin-left: auto; /* Empuja a la derecha */
        border-bottom-right-radius: 2px;
        text-align: right;
    }

    /* OPCIONES (BOTONES) */
    .chat-options {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }
    .option-btn {
        background: white;
        border: 1px solid #0077c8;
        color: #0077c8;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .option-btn:hover {
        background: #0077c8;
        color: white;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="chat-btn" onclick="toggleChat()">
    <i class="bi bi-chat-dots-fill"></i>
</div>

<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <div>
            <i class="bi bi-robot me-2"></i>Asistente DAC
        </div>
        <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
    </div>
    
    <div class="chat-body" id="chatBody">
        </div>
</div>

<script>
    // ESTADO INICIAL DEL BOT
    let chatOpen = false;
    
    function toggleChat() {
        const window = document.getElementById('chatWindow');
        chatOpen = !chatOpen;
        
        if (chatOpen) {
            window.style.display = 'flex';
            // Si está vacío, iniciamos la conversación
            if (document.getElementById('chatBody').innerHTML.trim() === '') {
                iniciarConversacion();
            }
        } else {
            window.style.display = 'none';
        }
    }

    function agregarMensaje(texto, tipo) {
        const chatBody = document.getElementById('chatBody');
        const div = document.createElement('div');
        div.className = `msg msg-${tipo}`;
        div.innerHTML = texto;
        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight; // Auto-scroll al final
    }

    function agregarOpciones(opciones) {
        const chatBody = document.getElementById('chatBody');
        const div = document.createElement('div');
        div.className = 'chat-options';
        
        opciones.forEach(op => {
            const btn = document.createElement('button');
            btn.className = 'option-btn';
            btn.innerText = op.texto;
            btn.onclick = () => responder(op);
            div.appendChild(btn);
        });
        
        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // --- LÓGICA DEL ÁRBOL DE DECISIONES ---

    function iniciarConversacion() {
        agregarMensaje("¡Hola! Soy Dac-Bot, tu asistente virtual. ¿En qué puedo ayudarte hoy?", "bot");
        agregarOpciones([
            { texto: "🔑 Problema de Clave", accion: "clave" },
            { texto: "🖨️ Impresora / Hardware", accion: "hardware" },
            { texto: "🌐 Internet / Red", accion: "red" },
            { texto: "👤 Contactar un Humano", accion: "humano" }
        ]);
    }

    function responder(opcion) {
        // 1. Mostrar lo que el usuario eligió
        agregarMensaje(opcion.texto, "user");
        
        // 2. Simular "escribiendo..." y responder
        setTimeout(() => {
            switch(opcion.accion) {
                case "clave":
                    agregarMensaje("Para restablecer tu clave, puedes usar la opción 'Olvidé mi contraseña' en la pantalla de login.", "bot");
                    agregarMensaje("¿Necesitas algo más?", "bot");
                    agregarOpciones([
                        { texto: "Sí, otra duda", accion: "inicio" },
                        { texto: "No, gracias", accion: "fin" }
                    ]);
                    break;

                case "hardware":
                    agregarMensaje("Por favor, verifica que los cables estén bien conectados y el equipo encendido.", "bot");
                    agregarMensaje("Si el problema persiste, lo mejor es crear un ticket.", "bot");
                    agregarOpciones([
                        { texto: "📝 Crear Ticket Ahora", accion: "crear_ticket" },
                        { texto: "Volver al inicio", accion: "inicio" }
                    ]);
                    break;
                
                case "red":
                    agregarMensaje("Intenta desconectar y conectar el cable de red. Si usas WiFi, verifica que estés en la red 'DAC-Corporativa'.", "bot");
                    agregarOpciones([
                         { texto: "Sigue sin funcionar", accion: "humano" },
                         { texto: "Ya funcionó", accion: "fin" }
                    ]);
                    break;

                case "humano":
                case "crear_ticket":
                    agregarMensaje("Entiendo. Te redirigiré al formulario para que un técnico te ayude.", "bot");
                    setTimeout(() => {
                        window.location.href = 'crear_ticket.php';
                    }, 2000);
                    break;

                case "inicio":
                    iniciarConversacion();
                    break;

                case "fin":
                    agregarMensaje("¡Perfecto! Que tengas un excelente día. 👋", "bot");
                    break;
            }
        }, 600);
    }
</script>