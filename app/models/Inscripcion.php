<?php
class Inscripcion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function yaInscrito($usuarioId, $cursoId, $nivelId = null) {
        $sql = "SELECT id FROM inscripciones
                WHERE usuario_id = :usuario_id AND curso_id = :curso_id
                AND (nivel_id IS NULL OR nivel_id = :nivel_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':curso_id'   => $cursoId,
            ':nivel_id'   => $nivelId
        ]);
        return $stmt->rowCount() > 0;
    }

    public function registrarCompra($datos) {
        $sql = "INSERT INTO inscripciones (usuario_id, curso_id, nivel_id, fecha_inscripcion, ultimo_ingreso, avance_porcentaje, nivel_actual, estatus_curso, forma_pago, precio_pagado)
                VALUES (:usuario_id, :curso_id, :nivel_id, NOW(), NOW(), 0, 1, 'En Progreso', :forma_pago, :precio_pagado)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':usuario_id'    => $datos['usuario_id'],
            ':curso_id'      => $datos['curso_id'],
            ':nivel_id'      => $datos['nivel_id'] ?? null,
            ':forma_pago'    => $datos['forma_pago'],
            ':precio_pagado' => $datos['precio_pagado']
        ]);
        return $this->conn->lastInsertId();
    }

    public function registrarAvance($inscripcionId, $numeroNivelCompletado, $cursoId) {
        $stmtTot = $this->conn->prepare("SELECT COUNT(*) FROM niveles WHERE curso_id = :curso_id");
        $stmtTot->execute([':curso_id' => $cursoId]);
        $totalNiveles = (int)$stmtTot->fetchColumn();

        if ($totalNiveles === 0) return false;

        $porcentaje = min(100, (int)round(($numeroNivelCompletado / $totalNiveles) * 100));
        $terminado = ($numeroNivelCompletado >= $totalNiveles);

        if ($terminado) {
            $sql = "UPDATE inscripciones
                    SET ultimo_ingreso = NOW(),
                        nivel_actual = :nivel_actual,
                        avance_porcentaje = 100,
                        estatus_curso = 'Terminado',
                        fecha_terminacion = COALESCE(fecha_terminacion, NOW())
                    WHERE id = :id";
        } else {
            $sql = "UPDATE inscripciones
                    SET ultimo_ingreso = NOW(),
                        nivel_actual = GREATEST(nivel_actual, :nivel_actual),
                        avance_porcentaje = GREATEST(avance_porcentaje, :avance)
                    WHERE id = :id";
        }

        $params = [':id' => $inscripcionId, ':nivel_actual' => $numeroNivelCompletado];
        if (!$terminado) {
            $params[':avance'] = $porcentaje;
        }

        $this->conn->prepare($sql)->execute($params);
        return $terminado;
    }

    public function obtenerInscripcionesUsuarioCurso($usuarioId, $cursoId) {
        $sql = "SELECT id, nivel_id, estatus_curso, avance_porcentaje
                FROM inscripciones
                WHERE usuario_id = :usuario_id AND curso_id = :curso_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':curso_id'   => $cursoId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerKardexUsuario($usuarioId) {
        $sql = "SELECT ins.id AS inscripcion_id, ins.curso_id, ins.nivel_id,
                       DATE(ins.fecha_inscripcion) AS fecha_inscripcion_raw,
                       DATE_FORMAT(ins.fecha_inscripcion, '%d-%b-%Y') AS fecha_inscripcion,
                       DATE_FORMAT(ins.ultimo_ingreso, '%d-%b-%Y') AS ultimo_ingreso,
                       DATE_FORMAT(ins.fecha_terminacion, '%d-%b-%Y') AS fecha_terminacion,
                       ins.avance_porcentaje, ins.nivel_actual, ins.estatus_curso,
                       c.titulo AS curso_titulo, c.estatus AS curso_estatus,
                       cat.nombre AS categoria_nombre,
                       u.nombre AS instructor_nombre,
                       (SELECT COUNT(*) FROM niveles n WHERE n.curso_id = c.id) AS total_niveles,
                       niv.titulo AS nivel_comprado_titulo, niv.numero_nivel AS nivel_comprado_numero
                FROM inscripciones ins
                INNER JOIN cursos c ON ins.curso_id = c.id
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                INNER JOIN usuarios u ON c.instructor_id = u.id
                LEFT JOIN niveles niv ON ins.nivel_id = niv.id
                WHERE ins.usuario_id = :usuario_id
                ORDER BY ins.ultimo_ingreso DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleReproductor($usuarioId, $cursoId) {
        $sqlIns = "SELECT * FROM inscripciones
                   WHERE usuario_id = :usuario_id AND curso_id = :curso_id
                   ORDER BY nivel_id ASC";
        $stmtIns = $this->conn->prepare($sqlIns);
        $stmtIns->execute([':usuario_id' => $usuarioId, ':curso_id' => $cursoId]);
        $inscripciones = $stmtIns->fetchAll(PDO::FETCH_ASSOC);

        if (empty($inscripciones)) {
            return null;
        }

        $this->conn->prepare("UPDATE inscripciones SET ultimo_ingreso = NOW() WHERE usuario_id = :u AND curso_id = :c")
                   ->execute([':u' => $usuarioId, ':c' => $cursoId]);

        $sqlCurso = "SELECT c.*, u.nombre AS instructor_nombre, cat.nombre AS categoria_nombre
                     FROM cursos c
                     INNER JOIN usuarios u ON c.instructor_id = u.id
                     INNER JOIN categorias cat ON c.categoria_id = cat.id
                     WHERE c.id = :id";
        $stmtCurso = $this->conn->prepare($sqlCurso);
        $stmtCurso->execute([':id' => $cursoId]);
        $curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

        $stmtNiv = $this->conn->prepare("SELECT * FROM niveles WHERE curso_id = :curso_id ORDER BY numero_nivel ASC");
        $stmtNiv->execute([':curso_id' => $cursoId]);
        $todosLosNiveles = $stmtNiv->fetchAll(PDO::FETCH_ASSOC);

        $tieneCompleto = false;
        $nivelesCompradosIds = [];
        foreach ($inscripciones as $ins) {
            if ($ins['nivel_id'] === null) {
                $tieneCompleto = true;
            } else {
                $nivelesCompradosIds[] = (int)$ins['nivel_id'];
            }
        }

        foreach ($todosLosNiveles as &$niv) {
            $niv['desbloqueado'] = $tieneCompleto || in_array((int)$niv['id'], $nivelesCompradosIds, true);
        }

        return [
            'curso'         => $curso,
            'inscripcion'   => $inscripciones[0],
            'tieneCompleto' => $tieneCompleto,
            'niveles'       => $todosLosNiveles
        ];
    }

    public function obtenerDiploma($inscripcionId, $usuarioId) {
        $sql = "SELECT ins.id, ins.estatus_curso, ins.avance_porcentaje,
                       DATE_FORMAT(ins.fecha_inscripcion, '%d/%m/%Y') AS fecha_inicio,
                       DATE_FORMAT(COALESCE(ins.fecha_terminacion, NOW()), '%d/%m/%Y') AS fecha_fin,
                       alu.nombre AS alumno_nombre,
                       c.titulo AS curso_titulo,
                       inst.nombre AS instructor_nombre
                FROM inscripciones ins
                INNER JOIN usuarios alu ON ins.usuario_id = alu.id
                INNER JOIN cursos c ON ins.curso_id = c.id
                INNER JOIN usuarios inst ON c.instructor_id = inst.id
                WHERE ins.id = :id AND ins.usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $inscripcionId, ':usuario_id' => $usuarioId]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos || ($datos['estatus_curso'] !== 'Terminado' && (int)$datos['avance_porcentaje'] < 100)) {
            return null;
        }
        return $datos;
    }
}