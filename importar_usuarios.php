<?php
// Conexión a tu BD (asumiendo que ya tienes tu archivo config)
include 'config/db.php'; 

// Verificamos si se subió un archivo
if (isset($_FILES['archivo_csv'])) {
    
    $archivo = $_FILES['archivo_csv']['tmp_name'];
    
    // Abrimos el archivo en modo lectura
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        
        // Leemos línea por línea
        while (($datos = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
            // Asignamos variables según el orden de columnas en el Excel
            $nombre_completo = $datos[0];
            $rut             = $datos[1];
            $email           = $datos[2];
            $telefono        = $datos[3];

            // ESTRATEGIA DE CONTRASEÑA:
            // Como el usuario es nuevo, usamos su RUT (sin guion ni puntos) 
            // como contraseña temporal por defecto.
            $password_temporal = str_replace(['.', '-'], '', $rut); 
            
            // IMPORTANTE: Siempre encriptar la contraseña
            $password_hash = password_hash($password_temporal, PASSWORD_DEFAULT);
            
            // Preparamos la consulta SQL
            $sql = "INSERT INTO users (nombre, rut, email, telefono, password, rol) 
                    VALUES (?, ?, ?, ?, ?, 'empleado')";
            
            $stmt = $conn->prepare($sql);
            
            // Ejecutamos la inserción. 
            // Usamos try-catch o verificamos errores para evitar duplicados (ej: emails repetidos)
            try {
                $stmt->execute([$nombre_completo, $rut, $email, $telefono, $password_hash]);
            } catch (PDOException $e) {
                // Aquí podrías guardar en un log qué usuarios fallaron
                continue; // Si falla uno, que siga con el siguiente
            }
        }
        
        fclose($handle);
        echo "Importación completada.";
    }
}
?>