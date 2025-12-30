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

Sigue estos pasos para correr el proyecto en tu máquina local:

1.  **Clonar el repositorio:**
    Descarga este proyecto dentro de tu carpeta `htdocs` en XAMPP.
    ```bash
    cd "D:\XAMPP\htdocs"
    git clone [https://github.com/09diego09/sistema_tickets.git](https://github.com/09diego09/sistema_tickets.git)
    ```

2.  **Base de Datos:**
    * Abre `phpMyAdmin` (http://localhost/phpmyadmin).
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

## ✒️ Autores

* **[Tu Nombre Completo]** - *Desarrollo Inicial* - [TuUsuarioDeGitHub]

---
⌨️ con ❤️ por Diego y Gemini jasjas