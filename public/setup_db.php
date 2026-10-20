<?php
require_once '../app/config/Database.php';

try {
    $db = (new Database())->getConnection();
    
    $hash = password_hash('12345', PASSWORD_DEFAULT);
    
    $db->exec("DELETE FROM usuarios WHERE email = 'admin@cxrse.com'");
    
    $query = "INSERT INTO usuarios (nombre, genero, fecha_nacimiento, email, password, rol, intentos_fallidos, estatus) 
              VALUES ('Milton Admin', 'Masculino', '2000-01-01', 'admin@cxrse.com', '$hash', 'Admin', 0, 'Activo')";
    
    $db->exec($query);
               
    echo "¡Usuario de prueba 'admin@cxrse.com' creado con éxito respetando tu estructura original!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>