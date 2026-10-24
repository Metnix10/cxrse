<?php

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    
    public $id;
    public $nombre;
    public $genero;
    public $fecha_nacimiento;
    public $foto;
    public $email;
    public $password;
    public $rol;
    public $intentos_fallidos;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function buscarPorEmail($email_ingresado) {
        $query = "SELECT id, nombre, email, password, rol, intentos_fallidos 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $email_ingresado = htmlspecialchars(strip_tags($email_ingresado));
        $stmt->bindParam(':email', $email_ingresado);
        $stmt->execute();

        return $stmt;
    }

    public function incrementarIntentosFallidos($usuario_id) {
        $query = "UPDATE " . $this->table_name . " SET intentos_fallidos = intentos_fallidos + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
    }

    public function reiniciarIntentosFallidos($usuario_id) {
        $query = "UPDATE " . $this->table_name . " SET intentos_fallidos = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
    }

    public function crear() {
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, genero, fecha_nacimiento, foto, email, password, rol, intentos_fallidos, estatus) 
                  VALUES (:nombre, :genero, :fecha_nacimiento, :foto, :email, :password, :rol, 0, 'Activo')";

        $stmt = $this->conn->prepare($query);

        
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->genero = htmlspecialchars(strip_tags($this->genero));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->foto = htmlspecialchars(strip_tags($this->foto)); 
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->rol = htmlspecialchars(strip_tags($this->rol));

        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':foto', $this->foto); 
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':rol', $this->rol);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>