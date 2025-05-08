<?php
class consultaFiltroSemana {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorSemana($week) {
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => [],'labelsLine' => [], 'dataLine' => []];
        $año = substr($week, 0, 4);
        $semana = substr($week, 6, 2);

        // Obtenemos el lunes de esa semana
        $start_date = new DateTime();
        $start_date->setISODate($año, $semana); // Lunes
        $start_datetime = $start_date->format('Y-m-d 00:00:00');

        // Obtenemos el domingo de esa semana
        $end_date = clone $start_date;
        $end_date->modify('+6 days');
        $end_datetime = $end_date->format('Y-m-d 23:59:59');

        //contulta para obtener total por dia de la semana
        $sql = "SELECT DATE(Fecha_Hora) as dia, SUM(Uso) as total
                FROM uso_escalera
                WHERE Fecha_Hora BETWEEN ? AND ?
                GROUP BY dia ORDER BY dia";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $start_datetime, $end_datetime);
        $stmt->execute();
        $result = $stmt->get_result();
        //consulta para obtener el total y el promedio de esa semana completa
        $sqlTotal = "SELECT SUM(Uso) as total, AVG(Uso) as promedio
                FROM uso_escalera
                WHERE Fecha_Hora BETWEEN ? AND ?";

        $stmtTotal = $this->conn->prepare($sqlTotal);
        $stmtTotal->bind_param("ss", $start_datetime, $end_datetime);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        //consulta para obtener el total de uso por hora de esa semana
        $sqlHora = "SELECT HOUR(Fecha_Hora) AS hora, SUM(Uso) as total
                        FROM uso_escalera
                        WHERE Fecha_Hora BETWEEN ? AND ?
                        GROUP BY hora
                        ORDER BY hora";
        $stmtHora = $this->conn->prepare($sqlHora);
        $stmtHora->bind_param("ss", $start_datetime, $end_datetime);
        $stmtHora->execute();
        $resultHora = $stmtHora->get_result();
        //guardado de los datos
        while ($row = $result->fetch_assoc()) {
            $response['labels'][] = $row['dia'];
            $response['data'][] = intval($row['total']);
        }   

        while ($row = $resultTotal->fetch_assoc()) {
            $response['total'][] = intval($row['total']);
            $response['promedio'][] = floatval($row['promedio']);
        } 

        $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
        while ($rowHora = $resultHora->fetch_assoc()) {
            $horas[intval($rowHora['hora'])] = intval($rowHora['total']);
        }

        foreach ($horas as $hora => $total) {
            $response['labelsLine'][] = str_pad($hora, 2, "0", STR_PAD_LEFT) . ":00";
            $response['dataLine'][] = $total;
        }

        return $response;
    }
}
?>