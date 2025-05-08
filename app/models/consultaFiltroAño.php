<?php
class consultaFiltroAño {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerDatosPorAño($año) {
        $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];
        // Obtenemos total de uso por mes
        $sql = "SELECT MONTH(Fecha_Hora) as mes, SUM(Uso) as total
                FROM uso_escalera
                WHERE YEAR(Fecha_Hora) = ?
                GROUP BY mes ORDER BY mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $año);
        $stmt->execute();
        $result = $stmt->get_result();

        $meses = array_fill(1, 12, 0); // Inicializa los 12 meses en 0

        $mesesEnEspanol = [
            1 => "enero", 
            2 => "febrero", 
            3 => "marzo", 
            4 => "abril", 
            5 => "mayo", 
            6 => "junio", 
            7 => "julio", 
            8 => "agosto", 
            9 => "septiembre", 
            10 => "octubre", 
            11 => "noviembre", 
            12 => "diciembre"
        ];

        //consulta para obtener el total y el promedio del mes seleccionado
        $sqlTotal = "SELECT SUM(Uso) as total, AVG(Uso) as promedio
                        FROM uso_escalera
                        WHERE YEAR(Fecha_Hora) = ?";
        $stmtTotal = $this->conn->prepare($sqlTotal);
        $stmtTotal->bind_param("i", $año);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        //consulta para obtener el total de uso por hora de ese mes
        $sqlHora = "SELECT HOUR(Fecha_Hora) AS hora, SUM(Uso) as total
                        FROM uso_escalera
                        WHERE YEAR(Fecha_Hora) = ?
                        GROUP BY hora
                        ORDER BY hora";
        $stmtHora = $this->conn->prepare($sqlHora);
        $stmtHora->bind_param("i", $año);
        $stmtHora->execute();
        $resultHora = $stmtHora->get_result();
        //guardado de los datos
        while ($row = $result->fetch_assoc()) {
            $meses[intval($row['mes'])] = intval($row['total']);
        }
        // Recorremos el arreglo de meses
        foreach ($meses as $numMes => $total) {
            // Usamos el array para obtener el nombre del mes en español
            $response['labels'][] = $mesesEnEspanol[$numMes]; // Nombre del mes en español
            $response['data'][] = $total;
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