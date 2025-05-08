<?php
class consultaClickAño {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorAño(int $año,String $mes) {
        $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];

        switch ($mes) {
            case "enero":
                $numMes = 1;
                break;
            case "febrero":
                $numMes = 2;
                break;
            case "marzo":
                $numMes = 3;
                break;
            case "abril":
                $numMes = 4;
                break;
            case "mayo":
                $numMes = 5;
                break;
            case "junio":
                $numMes = 6;
                break;
            case "julio":
                $numMes = 7;
                break;
            case "agosto":
                $numMes = 8;
                break;
            case "septiembre":
                $numMes = 9;
                break;
            case "octubre":
                $numMes = 10;
                break;
            case "noviembre":
                $numMes = 11;
                break;
            case "diciembre":
                $numMes = 12;
                break;
        }
        //Query para obtener total uso y el promedio de uso
        $sql = "SELECT MONTH(Fecha_Hora) as mes, SUM(Uso) as total, AVG(Uso) as promedio
                FROM uso_escalera
                WHERE YEAR(Fecha_Hora) = ? and MONTH(Fecha_Hora) = ?
                GROUP BY mes ORDER BY mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $año,$numMes);
        $stmt->execute();
        $result = $stmt->get_result();
        //Query para obtener total de uso por hora
        $sqlHora  = "SELECT HOUR(Fecha_Hora) AS hora, SUM(Uso) AS total
                FROM uso_escalera
                WHERE YEAR(Fecha_Hora) = ? AND MONTH(Fecha_Hora) = ?
                GROUP BY hora
                ORDER BY hora";
        $stmtHora  = $this->conn->prepare($sqlHora);
        $stmtHora ->bind_param("ii", $año,$numMes);
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