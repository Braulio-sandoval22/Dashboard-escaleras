<?php
class consultaFiltroDia {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorDia($fecha) {
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => [],'labelsLine' => [], 'dataLine' => []];

        $sql = "SELECT DATE(Fecha_Hora) as dia,SUM(Uso) as total, AVG(Uso) as promedio
                FROM uso_escalera
                WHERE DATE(Fecha_Hora) =?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();
        // Datos para el gráfico de líneas (por hora)
        $sqlHora = "SELECT HOUR(Fecha_Hora) as hora, SUM(Uso) as total
                FROM uso_escalera
                WHERE DATE(Fecha_Hora) = ?
                GROUP BY hora ORDER BY hora";
        $stmtHora = $this->conn->prepare($sqlHora);
        $stmtHora->bind_param("s", $fecha);
        $stmtHora->execute();
        $resultHora = $stmtHora->get_result();

        while ($row = $result->fetch_assoc()) {
            $response['labels'][] = $row['dia'];
            $response['data'][] = intval($row['total']);
            $response['total'][] = intval($row['total']);
            $response['promedio'][] = floatval($row['promedio']);
        }  
    
        $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
        while ($row = $resultHora->fetch_assoc()) {
            $horas[intval($row['hora'])] = intval($row['total']);
        }

        foreach ($horas as $hora => $total) {
            $response['labelsLine'][] = str_pad($hora, 2, "0", STR_PAD_LEFT) . ":00";
            $response['dataLine'][] = $total;
        }

        return $response;
    }
}
?>