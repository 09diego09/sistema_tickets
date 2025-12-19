<?php
require 'config/db.php';

if ($pdo) {
    echo "<h1>¡Todo listo! ✅</h1>";
    echo "<p>PHP se conectó correctamente a MySQL.</p>";
}
?>