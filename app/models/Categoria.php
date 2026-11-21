<?php
class Categoria {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarTodas() {
        $sql = "SELECT id, nombre, descripcion,
                       DATE_FORMAT(fecha_creacion, '%d-%b-%Y') AS fecha_creacion
                FROM categorias
                ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $descripcion, $usuarioId) {
        $sql = "INSERT INTO categorias (nombre, descripcion, usuario_id, fecha_creacion)
                VALUES (:nombre, :descripcion, :usuario_id, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre'      => trim($nombre),
            ':descripcion' => trim($descripcion),
            ':usuario_id'  => $usuarioId
        ]);
        return $this->conn->lastInsertId();
    }

    public function actualizar($id, $nombre, $descripcion) {
        $sql = "UPDATE categorias
                SET nombre = :nombre, descripcion = :descripcion
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id'          => $id,
            ':nombre'      => trim($nombre),
            ':descripcion' => trim($descripcion)
        ]);
        return $stmt->rowCount() >= 0;
    }

    public function eliminar($id) {
        $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM cursos WHERE categoria_id = :id");
        $stmtCheck->execute([':id' => $id]);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar esta categoría porque tiene cursos asociados.");
        }

        $stmt = $this->conn->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}