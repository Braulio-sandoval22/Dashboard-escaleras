<?php
class consultaFiltroMes {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorMes($mesSeleccionado) {
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => [],'labelsLine' => [], 'dataLine' => []];

        // Obtener primer día del mes
        $fechaInicio = date($mesSeleccionado . '-01 00:00:00');

        // Obtener último día del mes
        $fechaFin = date("Y-m-t 23:59:59", strtotime($fechaInicio));

        // Consulta SQL para agrupar por semana del mes (semanas 1, 2, 3, 4)
        $sql = "SELECT 
                    FLOOR((DAY(Fecha_Hora) - 1) / 7) + 1 AS semana_del_mes, 
                    SUM(Uso) as total
                FROM uso_escalera
                WHERE Fecha_Hora BETWEEN ? AND ?
                GROUP BY semana_del_mes
                ORDER BY semana_del_mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();
        //consulta para obtener el total y el promedio del mes seleccionado
        $sqlTotal = "SELECT SUM(Uso) as total, AVG(Uso) as promedio
                        FROM uso_escalera
                        WHERE Fecha_Hora BETWEEN ? AND ?";
        $stmtTotal = $this->conn->prepare($sqlTotal);
        $stmtTotal->bind_param("ss", $fechaInicio, $fechaFin);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        //consulta para obtener el total de uso por hora de ese mes
        $sqlHora = "SELECT HOUR(Fecha_Hora) AS hora, SUM(Uso) as total
                        FROM uso_escalera
                        WHERE Fecha_Hora BETWEEN ? AND ?
                        GROUP BY hora
                        ORDER BY hora;";
        $stmtHora = $this->conn->prepare($sqlHora);
        $stmtHora->bind_param("ss", $fechaInicio, $fechaFin);
        $stmtHora->execute();
        $resultHora = $stmtHora->get_result();
        //guardado de los datos
    
        while ($row = $result->fetch_assoc()) {
            $response['labels'][] = 'Semana ' . $row['semana_del_mes']; // Etiqueta para la semana
            $response['data'][] = intval($row['total']); // Total de uso en esa semana
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