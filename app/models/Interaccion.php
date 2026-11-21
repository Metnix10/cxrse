<?php
class Interaccion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarComentariosCurso($cursoId) {
        $sql = "SELECT com.id, com.comentario, com.calificacion, com.eliminado,
                       DATE_FORMAT(com.fecha_envio, '%d-%b-%Y %H:%i') AS fecha,
                       u.nombre AS usuario_nombre, u.foto AS usuario_foto
                FROM comentarios com
                INNER JOIN usuarios u ON com.usuario_id = u.id
                WHERE com.curso_id = :curso_id
                ORDER BY com.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':curso_id' => $cursoId]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as &$fila) {
            if ((int)$fila['eliminado'] === 1) {
                $fila['comentario'] = 'Este comentario fue eliminado por un administrador.';
            }
        }
        return $filas;
    }

    public function crearComentario($usuarioId, $cursoId, $calificacion, $comentario) {
        $stmtIns = $this->conn->prepare(
            "SELECT id FROM inscripciones
             WHERE usuario_id = :u AND curso_id = :c
               AND (estatus_curso = 'Terminado' OR avance_porcentaje >= 100)
             LIMIT 1"
        );
        $stmtIns->execute([':u' => $usuarioId, ':c' => $cursoId]);
        if ($stmtIns->rowCount() === 0) {
            throw new Exception("Debes finalizar el curso completo (100%) antes de poder calificar y comentar.");
        }

        $sql = "INSERT INTO comentarios (curso_id, usuario_id, calificacion, comentario, eliminado)
                VALUES (:curso_id, :usuario_id, :calificacion, :comentario, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':curso_id'     => $cursoId,
            ':usuario_id'   => $usuarioId,
            ':calificacion' => max(1, min(5, (int)$calificacion)),
            ':comentario'   => trim($comentario)
        ]);
        return $this->conn->lastInsertId();
    }

    public function obtenerConversacion($cursoId, $usuarioA, $usuarioB) {
        $sql = "SELECT m.id, m.remitente_id, m.destinatario_id, m.mensaje,
                       DATE_FORMAT(m.fecha_envio, '%d-%b-%Y %H:%i') AS fecha,
                       u.nombre AS remitente_nombre, u.rol AS remitente_rol, u.foto AS remitente_foto
                FROM mensajes m
                INNER JOIN usuarios u ON m.remitente_id = u.id
                WHERE m.curso_id = :curso_id
                  AND ((m.remitente_id = :a1 AND m.destinatario_id = :b1)
                    OR (m.remitente_id = :b2 AND m.destinatario_id = :a2))
                ORDER BY m.fecha_envio ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':curso_id' => $cursoId,
            ':a1' => $usuarioA, ':b1' => $usuarioB,
            ':b2' => $usuarioB, ':a2' => $usuarioA
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarBandejaInstructor($instructorId) {
        $sql = "SELECT DISTINCT m.curso_id, c.titulo AS curso_titulo,
                       alu.id AS alumno_id, alu.nombre AS alumno_nombre, alu.foto AS alumno_foto,
                       (SELECT m2.mensaje FROM mensajes m2
                        WHERE m2.curso_id = m.curso_id
                          AND ((m2.remitente_id = alu.id AND m2.destinatario_id = :inst1)
                            OR (m2.remitente_id = :inst2 AND m2.destinatario_id = alu.id))
                        ORDER BY m2.fecha_envio DESC LIMIT 1) AS ultimo_mensaje
                FROM mensajes m
                INNER JOIN cursos c ON m.curso_id = c.id
                INNER JOIN usuarios alu ON (CASE WHEN m.remitente_id = :inst3 THEN m.destinatario_id ELSE m.remitente_id END) = alu.id
                WHERE c.instructor_id = :inst4
                ORDER BY m.fecha_envio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':inst1' => $instructorId,
            ':inst2' => $instructorId,
            ':inst3' => $instructorId,
            ':inst4' => $instructorId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enviarMensaje($cursoId, $remitenteId, $destinatarioId, $mensaje) {
        $sql = "INSERT INTO mensajes (curso_id, remitente_id, destinatario_id, mensaje, fecha_envio)
                VALUES (:curso_id, :remitente_id, :destinatario_id, :mensaje, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':curso_id'        => $cursoId,
            ':remitente_id'    => $remitenteId,
            ':destinatario_id' => $destinatarioId,
            ':mensaje'         => trim($mensaje)
        ]);
        return $this->conn->lastInsertId();
    }

    public function listarTodosComentariosAdmin() {
        $sql = "SELECT com.id, com.comentario, com.calificacion, com.eliminado, com.causa_eliminacion,
                       DATE_FORMAT(com.fecha_envio, '%d-%b-%Y %H:%i') AS fecha_envio,
                       c.titulo AS curso_titulo,
                       u.nombre AS alumno_nombre, u.email AS alumno_email
                FROM comentarios com
                INNER JOIN cursos c ON com.curso_id = c.id
                INNER JOIN usuarios u ON com.usuario_id = u.id
                ORDER BY com.eliminado ASC, com.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bajaLogicaComentario($comentarioId, $causa) {
        $sql = "UPDATE comentarios
                SET eliminado = 1,
                    fecha_eliminacion = NOW(),
                    causa_eliminacion = :causa
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id'    => $comentarioId,
            ':causa' => trim($causa)
        ]);
        return $stmt->rowCount() > 0;
    }
}