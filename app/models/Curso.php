<?php
class Curso {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearConNiveles($datosCurso, $niveles) {
        try {
            $this->conn->beginTransaction();

            $sqlCurso = "INSERT INTO cursos (titulo, descripcion, categoria_id, instructor_id, precio, imagen, estatus, fecha_creacion)
                         VALUES (:titulo, :descripcion, :categoria_id, :instructor_id, :precio, :imagen, 'Activo', NOW())";
            $stmt = $this->conn->prepare($sqlCurso);
            $stmt->execute([
                ':titulo'        => $datosCurso['titulo'],
                ':descripcion'   => $datosCurso['descripcion'],
                ':categoria_id'  => $datosCurso['categoria_id'],
                ':instructor_id' => $datosCurso['instructor_id'],
                ':precio'        => $datosCurso['precio'] ?? 0.00,
                ':imagen'        => $datosCurso['imagen']
            ]);

            $cursoId = $this->conn->lastInsertId();

            $sqlNivel = "INSERT INTO niveles (curso_id, numero_nivel, titulo, descripcion, video_url, precio, pdf_url, link_externo, imagen_url, fecha_creacion)
                         VALUES (:curso_id, :numero_nivel, :titulo, :descripcion, :video_url, :precio, :pdf_url, :link_externo, :imagen_url, NOW())";
            $stmtNivel = $this->conn->prepare($sqlNivel);

            foreach ($niveles as $index => $nivel) {
                $stmtNivel->execute([
                    ':curso_id'     => $cursoId,
                    ':numero_nivel' => $index + 1,
                    ':titulo'       => $nivel['titulo'],
                    ':descripcion'  => $nivel['descripcion'] ?? null,
                    ':video_url'    => $nivel['video_url'],
                    ':precio'       => $nivel['precio'] ?? 0.00,
                    ':pdf_url'      => $nivel['pdf_url'] ?? null,
                    ':link_externo' => $nivel['link_externo'] ?? null,
                    ':imagen_url'   => $nivel['imagen_url'] ?? null
                ]);
            }

            $this->conn->commit();
            return $cursoId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function buscarActivos($filtros = []) {
        $sql = "SELECT c.*, u.nombre AS instructor_nombre, cat.nombre AS categoria_nombre,
                       COALESCE(AVG(CASE WHEN com.eliminado = 0 THEN com.calificacion END), 0) AS promedio_calificacion,
                       COUNT(DISTINCT ins.id) AS total_inscritos
                FROM cursos c
                INNER JOIN usuarios u ON c.instructor_id = u.id
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN comentarios com ON c.id = com.curso_id
                LEFT JOIN inscripciones ins ON c.id = ins.curso_id
                WHERE c.estatus = 'Activo'";
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND c.categoria_id = :categoria_id";
            $params[':categoria_id'] = $filtros['categoria_id'];
        }
        if (!empty($filtros['titulo'])) {
            $sql .= " AND c.titulo LIKE :titulo";
            $params[':titulo'] = '%' . $filtros['titulo'] . '%';
        }
        if (!empty($filtros['instructor'])) {
            $sql .= " AND u.nombre LIKE :instructor";
            $params[':instructor'] = '%' . $filtros['instructor'] . '%';
        }
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(c.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        $sql .= " GROUP BY c.id";

        $orden = $filtros['orden'] ?? 'recientes';
        if ($orden === 'calificados') {
            $sql .= " ORDER BY promedio_calificacion DESC";
        } elseif ($orden === 'vendidos') {
            $sql .= " ORDER BY total_inscritos DESC";
        } else {
            $sql .= " ORDER BY c.fecha_creacion DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bajaLogica($cursoId, $instructorId) {
        $sql = "UPDATE cursos SET estatus = 'Inactivo' WHERE id = :id AND instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $cursoId, ':instructor_id' => $instructorId]);
        return $stmt->rowCount() > 0;
    }

    public function obtenerDetalle($cursoId) {
        $sql = "SELECT c.*, u.nombre AS instructor_nombre, cat.nombre AS categoria_nombre,
                       COALESCE(AVG(CASE WHEN com.eliminado = 0 THEN com.calificacion END), 0) AS promedio_calificacion
                FROM cursos c
                INNER JOIN usuarios u ON c.instructor_id = u.id
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN comentarios com ON c.id = com.curso_id
                WHERE c.id = :id
                GROUP BY c.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $cursoId]);
        $curso = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$curso) return null;

        $stmtNiv = $this->conn->prepare("SELECT * FROM niveles WHERE curso_id = :curso_id ORDER BY numero_nivel ASC");
        $stmtNiv->execute([':curso_id' => $cursoId]);
        $curso['niveles'] = $stmtNiv->fetchAll(PDO::FETCH_ASSOC);

        return $curso;
    }

    public function obtenerReporteInstructor($instructorId) {
        $sql = "SELECT c.id, c.titulo, c.precio, c.estatus, DATE(c.fecha_creacion) AS fecha_creacion,
                       cat.nombre AS categoria_nombre,
                       (SELECT COUNT(*) FROM niveles n WHERE n.curso_id = c.id) AS total_niveles,
                       COUNT(ins.id) AS total_alumnos,
                       COALESCE(AVG(ins.nivel_actual), 0) AS nivel_promedio,
                       COALESCE(SUM(ins.precio_pagado), 0) AS total_ingresos
                FROM cursos c
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN inscripciones ins ON c.id = ins.curso_id
                WHERE c.instructor_id = :instructor_id
                GROUP BY c.id
                ORDER BY c.fecha_creacion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':instructor_id' => $instructorId]);
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlAlumnos = "SELECT ins.id, u.nombre AS alumno_nombre,
                              DATE_FORMAT(ins.fecha_inscripcion, '%d-%b-%Y') AS fecha_inscripcion,
                              ins.nivel_actual, ins.avance_porcentaje, ins.precio_pagado, ins.forma_pago
                       FROM inscripciones ins
                       INNER JOIN usuarios u ON ins.usuario_id = u.id
                       WHERE ins.curso_id = :curso_id
                       ORDER BY ins.fecha_inscripcion DESC";
        $stmtAlumnos = $this->conn->prepare($sqlAlumnos);

        foreach ($cursos as &$curso) {
            $stmtAlumnos->execute([':curso_id' => $curso['id']]);
            $curso['alumnos'] = $stmtAlumnos->fetchAll(PDO::FETCH_ASSOC);
        }

        return $cursos;
    }
}