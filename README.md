🎫 DAC Controls HelpDesk - Sistema de Tickets
Sistema de Mesa de Ayuda (HelpDesk) desarrollado para la gestión, seguimiento y resolución centralizada de incidentes técnicos y tareas de soporte TI. Este proyecto utiliza una arquitectura MVC (Modelo-Vista-Controlador) para garantizar un código escalable y organizado.

🚀 Características Principales
Gestión de Roles: Niveles de acceso diferenciados para Administradores, Técnicos y Usuarios finales.

Ciclo de Vida del Ticket: Seguimiento completo de estados (Abierto, En Proceso, Resuelto y Cerrado).

Módulo de Tareas: Gestión de labores diarias independiente de los reportes de fallas.

Notificaciones: Sistema preparado para alertas vía correo electrónico (SMTP).

Panel Administrativo: Dashboard con métricas clave para la toma de decisiones.

🛠️ Tecnologías Utilizadas
Backend: PHP 8.x (Nativo) bajo arquitectura MVC.

Base de Datos: MySQL / MariaDB (Gestionado con MySQL Workbench).

Frontend: HTML5, CSS3 (Diseño corporativo), JavaScript.

Entorno de Desarrollo: XAMPP (Servidor Apache).

⚙️ Instalación y Configuración
Clonar el Repositorio: Copia el proyecto dentro de la carpeta htdocs de tu instalación de XAMPP:

Bash

git clone https://github.com/TuUsuario/sistema_tickets.git
Configuración de la Base de Datos:

Utiliza MySQL Workbench o phpMyAdmin para crear la base de datos: sistema_tickets_db.

Importa el archivo .sql ubicado en la carpeta /database para generar las tablas y la estructura necesaria.

Ajustes de Conexión:

Dirígete a la carpeta /config.

Edita el archivo de conexión con tus credenciales locales (por defecto en XAMPP: user: root, password: "").

Ejecución:

Inicia los módulos de Apache y MySQL en XAMPP.

Accede desde tu navegador a: http://localhost/sistema_tickets

📁 Estructura del Proyecto
/actions: Lógica de procesamiento y controladores de formularios.

/assets: Recursos estáticos (CSS, JS, Imágenes).

/config: Parámetros de conexión a la base de datos.

/views: Interfaces de usuario y vistas finales.

/includes: Fragmentos reutilizables (Modales, headers, footers).

⌨️ con ❤️ por Diego y Gemini 🚀
