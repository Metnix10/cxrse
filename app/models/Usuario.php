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
    public $estatus;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function buscarPorEmail($email_ingresado) {
        $query = "SELECT id, nombre, email, password, rol, foto, intentos_fallidos, estatus 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 1";

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
        $this->foto = filter_var(trim($this->foto), FILTER_SANITIZE_URL);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->rol = htmlspecialchars(strip_tags($this->rol));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':rol', $this->rol);

        return $stmt->execute();
    }

    public function listarTodosParaAdmin() {
        $sql = "SELECT id, nombre, email, rol, intentos_fallidos, estatus,
                       DATE_FORMAT(fecha_registro, '%d-%b-%Y') AS fecha_registro
                FROM usuarios
                ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstatus($usuarioId, $nuevoEstatus) {
        $intentos = ($nuevoEstatus === 'Activo') ? 0 : 3;
        $sql = "UPDATE usuarios
                SET estatus = :estatus, intentos_fallidos = :intentos
                WHERE id = :id AND rol <> 'Admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':estatus'  => $nuevoEstatus,
            ':intentos' => $intentos,
            ':id'       => $usuarioId
        ]);
        return $stmt->rowCount() > 0;
    }

    public function obtenerReporteInstructores() {
        $sql = "SELECT u.id, u.email, u.nombre,
                       DATE_FORMAT(u.fecha_registro, '%d-%b-%Y') AS fecha_ingreso,
                       COUNT(DISTINCT c.id) AS cursos_ofrecidos,
                       COALESCE(SUM(ins.precio_pagado), 0) AS total_ganancias
                FROM usuarios u
                LEFT JOIN cursos c ON u.id = c.instructor_id
                LEFT JOIN inscripciones ins ON c.id = ins.curso_id
                WHERE u.rol = 'Instructor'
                GROUP BY u.id
                ORDER BY total_ganancias DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerReporteEstudiantes() {
        $sql = "SELECT u.id, u.email, u.nombre,
                       DATE_FORMAT(u.fecha_registro, '%d-%b-%Y') AS fecha_ingreso,
                       COUNT(ins.id) AS cursos_inscritos,
                       COALESCE(ROUND((SUM(CASE WHEN ins.estatus_curso = 'Terminado' OR ins.avance_porcentaje >= 100 THEN 1 ELSE 0 END) / NULLIF(COUNT(ins.id), 0)) * 100, 1), 0) AS porcentaje_terminados
                FROM usuarios u
                LEFT JOIN inscripciones ins ON u.id = ins.usuario_id
                WHERE u.rol = 'Estudiante'
                GROUP BY u.id
                ORDER BY cursos_inscritos DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT id, nombre, genero, fecha_nacimiento, foto, email, rol,
                         DATE_FORMAT(fecha_registro, '%d/%m/%Y') AS fecha_registro
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPerfil($id, $datos) {
        $campos = [
            "nombre = :nombre",
            "genero = :genero",
            "fecha_nacimiento = :fecha_nacimiento"
        ];
        $params = [
            ':id'               => $id,
            ':nombre'           => htmlspecialchars(strip_tags(trim($datos['nombre']))),
            ':genero'           => htmlspecialchars(strip_tags(trim($datos['genero']))),
            ':fecha_nacimiento' => htmlspecialchars(strip_tags(trim($datos['fecha_nacimiento'])))
        ];

        if (!empty($datos['foto'])) {
            $campos[] = "foto = :foto";
            $params[':foto'] = filter_var(trim($datos['foto']), FILTER_SANITIZE_URL);
        }

        if (!empty($datos['password'])) {
            $campos[] = "password = :password";
            $params[':password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $campos) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
}