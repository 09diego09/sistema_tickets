# 🎫 DAC CONTROLS - SISTEMA DE GESTIÓN DE TICKETS Y SOPORTE
# Versión 1.0 - Documentación del Proyecto

## 📋 DESCRIPCIÓN GENERAL
Este sistema es una plataforma web integral diseñada para la recepción, seguimiento y resolución de requerimientos técnicos (Tickets) y asignación de labores diarias (Tareas). El objetivo principal es centralizar la comunicación interna de DAC Controls, eliminando la dispersión de información en correos o chats informales.

Desarrollado con una arquitectura limpia bajo el patrón de diseño MVC (Modelo-Vista-Controlador).

---

## 🚀 CARACTERÍSTICAS PRINCIPALES
* Gestión de Roles: Permisos diferenciados para Administradores (Gerencia/TI), Técnicos y Usuarios finales.
* Módulo de Tickets: Ciclo de vida completo desde la apertura hasta el cierre con historial de cambios.
* Panel de Tareas: Control de actividades diarias con estados de avance en tiempo real.
* Interfaz Profesional: Diseño limpio, intuitivo y adaptado para un entorno corporativo.
* Escalabilidad: Preparado para futuras integraciones con Microsoft Teams y alertas SMTP.

---

## 🛠️ STACK TECNOLÓGICO
* LENGUAJE: PHP 8.x (Arquitectura MVC)
* BASE DE DATOS: MySQL / MariaDB (Diseñada en MySQL Workbench)
* FRONTEND: HTML5, CSS3, JavaScript (Vanilla)
* SERVIDOR DE DESARROLLO: XAMPP / Apache

---

## ⚙️ GUÍA DE INSTALACIÓN (ENTORNO LOCAL)

1. PREPARACIÓN DEL DIRECTORIO:
   Clonar o descargar el repositorio dentro de la carpeta 'htdocs' de XAMPP:
   Ruta sugerida: C:\xampp\htdocs\sistema_tickets\

2. CONFIGURACIÓN DE BASE DE DATOS:
   - Iniciar los servicios de Apache y MySQL en el panel de XAMPP.
   - Crear una base de datos denominada 'sistema_tickets_db' en MySQL Workbench o phpMyAdmin.
   - Importar el archivo SQL ubicado en la carpeta '/database' del proyecto.

3. CONEXIÓN AL SERVIDOR:
   - Localizar el archivo de configuración en '/config/database.php' (o similar).
   - Verificar que las credenciales coincidan con las de su servidor local (Host: localhost, Usuario: root, Pass: "").

4. ACCESO AL SISTEMA:
   - Abrir el navegador web y navegar a: http://localhost/sistema_tickets/

---

## 📁 ESTRUCTURA DE DIRECTORIOS
* /actions   -> Controladores y procesamiento de datos.
* /assets    -> Recursos estáticos (Estilos CSS, JS, imágenes corporativas).
* /config    -> Parámetros del sistema y conexión a BD.
* /views     -> Vistas de usuario (Interfaces de tickets, dashboard y login).
* /includes  -> Componentes reutilizables (Navegación, modales, headers y footers).

---

## 📝 NOTAS DE DESARROLLO
Este proyecto se encuentra en fase de validación de prototipo. La arquitectura está diseñada para facilitar la migración a un hosting profesional (VPS), permitiendo la salida a producción y la habilitación de correos corporativos seguros.

---
⌨️ Desarrollado con dedicación por Diego (y el apoyo constante de Gemini) para DAC Controls. 🚀
