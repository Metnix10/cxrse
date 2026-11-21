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
}