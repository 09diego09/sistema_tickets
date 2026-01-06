# 🎫 Sistema de Tickets

Este es un sistema web desarrollado para la gestión, seguimiento y resolución de tickets de soporte técnico. El proyecto está construido siguiendo el patrón MVC (Modelo-Vista-Controlador) y está optimizado para funcionar en un entorno local con XAMPP.

## 🚀 Características Principales

* **Gestión de Usuarios:** Registro e inicio de sesión.
* **Creación de Tickets:** Los usuarios pueden reportar incidentes.
* **Seguimiento:** Estado de los tickets (Abierto, En proceso, Cerrado).
* **Panel Administrativo:** Gestión centralizada de las solicitudes.

## 🛠️ Tecnologías Utilizadas

* **Lenguaje Principal:** PHP (Nativo).
* **Base de Datos:** MySQL / MariaDB.
* **Servidor Local:** XAMPP (Apache).
* **Frontend:** HTML5, CSS3, JavaScript.

## ⚙️ Instalación y Configuración

# 🎫 Sistema de Tickets - DAC Controls HelpDesk

Sistema de Mesa de Ayuda (HelpDesk) desarrollado en PHP y MySQL para la gestión centralizada de incidentes técnicos y soporte TI.

![Estado del Proyecto](https://img.shields.io/badge/Estado-EnProceso-success)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1)

## 📋 Características

- **Roles de Usuario:** Sistema de permisos para Administradores, Técnicos y Usuarios finales.
- **Gestión de Tickets:** Ciclo de vida completo (Abierto, En Proceso, Resuelto).
- **Notificaciones SMTP:** Envío automático de correos electrónicos al actualizar tickets.
- **Archivos Adjuntos:** Soporte para evidencias en imágenes y PDF.
- **Dashboard:** Gráficos estadísticos en tiempo real.

## 🚀 Instalación

1. Clonar el repositorio en `htdocs`:
   ```bash
   git clone [https://github.com/TU_USUARIO/sistema_tickets.git](https://github.com/TU_USUARIO/sistema_tickets.git)

2.  **Base de Datos:**
    * Abre `phpMyAdmin` (http://localhost/phpmyadmin). o puedes utilizar mysql Workbench, con eso he trabajado, el phpmyadmin es sugerencia de gemini, la herramienta con la que me estoy apoyando
    * Crea una base de datos llamada `sistema_tickets_db` (o el nombre que prefieras).
    * Importa el archivo SQL ubicado en la carpeta `database/` (si existe) o ejecuta el script de creación.

3.  **Configuración:**
    * Ve a la carpeta `config/`.
    * Asegúrate de que los datos de conexión (usuario, contraseña y nombre de BD) coincidan con los de tu XAMPP.

4.  **Ejecutar:**
    * Abre tu navegador y entra a: `http://localhost/sistema_tickets`

## 📁 Estructura del Proyecto

* `/actions`: Lógica de procesamiento de formularios.
* `/assets`: Imágenes y recursos estáticos.
* `/config`: Archivos de conexión a la base de datos.
* `/views`: Las pantallas que ve el usuario (HTML/PHP).
* `/includes`: Fragmentos de código reutilizables (header, footer).

---
⌨️ con ❤️ por Diego y Gemini jasjas
