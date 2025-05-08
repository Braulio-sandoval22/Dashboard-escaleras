<?php
class consultaClickSemana {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorSemana($dia) {
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];
        $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
        // Obtenemos el total de uso y el promedio
        $sql = "SELECT SUM(Uso) as total, AVG(Uso) as promedio
                FROM uso_escalera
                WHERE DATE(Fecha_Hora) =?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $dia);
        $stmt->execute();
        $result = $stmt->get_result();
        // Obtenemos el total de uso por hora
        $sqlHora = "SELECT HOUR(Fecha_Hora) as hora, SUM(Uso) as total
                    FROM uso_escalera
                    WHERE DATE(Fecha_Hora) =?
                    GROUP BY hora ORDER BY hora";
        $stmtHora  = $this->conn->prepare($sqlHora);
        $stmtHora ->bind_param("s", $dia);
        $stmtHora ->execute();
        $resultHora  = $stmtHora->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $response['total'][] = intval($row['total']);
            $response['promedio'][] = floatval($row['promedio']);
        }
        
        while ($rowHora = $resultHora->fetch_assoc()) {
            $horas[intval($rowHora['hora'])] = intval($rowHora['total']);
        }

        foreach ($horas as $hora => $total) {
            $response['labels'][] = str_pad($hora, 2, "0", STR_PAD_LEFT) . ":00";
            $response['data'][] = $total;
        }

        return $response;
    }
}
?>