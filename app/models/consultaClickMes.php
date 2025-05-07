<?php
class consultaClickMes {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorMes(String $mesSeleccionado, String $semana) {
        $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];

        $numSemana = (int) filter_var($semana, FILTER_SANITIZE_NUMBER_INT);
        if ($numSemana < 1 || $numSemana > 4) {
            $numSemana = 5;
        }
        // Obtener primer día del mes
        $fechaInicio = date($mesSeleccionado . '-01 00:00:00');
        // Obtener último día del mes
        $fechaFin = date("Y-m-t 23:59:59", strtotime($fechaInicio));
        
        $sql =  "SELECT semana_del_mes AS semana, total, promedio
                    FROM (
                        SELECT 
                            FLOOR((DAY(Fecha_Hora) - 1) / 7) + 1 AS semana_del_mes, 
                            SUM(Uso) AS total,
                            AVG(Uso) AS promedio
                        FROM uso_escalera
                        WHERE Fecha_Hora BETWEEN ? AND ?
                        GROUP BY semana_del_mes
                        ORDER BY semana_del_mes
                    ) AS subquery
                    WHERE semana_del_mes = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $fechaInicio, $fechaFin,$numSemana);
        $stmt->execute();
        $result = $stmt->get_result();

        $sqlHora  = "SELECT semana_del_mes, hora, total
                    FROM (
                        SELECT 
                            FLOOR((DAY(Fecha_Hora) - 1) / 7) + 1 AS semana_del_mes,
                            HOUR(Fecha_Hora) AS hora,
                            SUM(Uso) AS total
                        FROM uso_escalera
                        WHERE Fecha_Hora BETWEEN ? AND ?
                        GROUP BY semana_del_mes, hora
                        ORDER BY semana_del_mes, hora
                    ) AS subquery
                    WHERE semana_del_mes = ?";
        $stmtHora  = $this->conn->prepare($sqlHora);
        $stmtHora ->bind_param("ssi", $fechaInicio,$fechaFin,$numSemana);
        $stmtHora ->execute();
        $resultHora  = $stmtHora->get_result();


        while ($row = $result->fetch_assoc()) {
            $response['total'][] = intval($row['total']); // Etiqueta para la semana
            $response['promedio'][] = floatval($row['promedio']); // Total de uso en esa semana
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