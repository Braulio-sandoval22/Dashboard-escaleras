<?php

if (isset($_GET['tipo'])) {
    include 'conexion.php';

    $tipo = $_GET['tipo'];
    $response = ['labels' => [], 'data' => []];

    switch ($tipo) {
        case 'año':
            $año = intval($_GET['año']);
            $sql = "SELECT MONTH(Fecha_Hora) as mes, SUM(Uso) as total
                    FROM uso_escalera
                    WHERE YEAR(Fecha_Hora) = ?
                    GROUP BY mes ORDER BY mes";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $año);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $meses = array_fill(1, 12, 0); // Inicializa los 12 meses en 0
            while ($row = $result->fetch_assoc()) {
                $meses[intval($row['mes'])] = intval($row['total']);
            }
            
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
            
            // Recorremos el arreglo de meses
            foreach ($meses as $numMes => $total) {
                // Usamos el array para obtener el nombre del mes en español
                $response['labels'][] = $mesesEnEspanol[$numMes]; // Nombre del mes en español
                $response['data'][] = $total;
            }
            

            break;
    
        case 'mes':
            $mesSeleccionado = $_GET['mes']; // Ejemplo: "2025-04"

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

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $fechaInicio, $fechaFin);
            $stmt->execute();
            $result = $stmt->get_result();

            $response = ['labels' => [], 'data' => []];

            while ($row = $result->fetch_assoc()) {
                $response['labels'][] = 'Semana ' . $row['semana_del_mes']; // Etiqueta para la semana
                $response['data'][] = intval($row['total']); // Total de uso en esa semana
            }
            break;
    
        case 'semana':
            $week = $_GET['semana']; // Ejemplo: "2025-W14"
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

            // Ahora sí, hacemos la consulta
            $sql = "SELECT DATE(Fecha_Hora) as dia, SUM(Uso) as total
                    FROM uso_escalera
                    WHERE Fecha_Hora BETWEEN ? AND ?
                    GROUP BY dia ORDER BY dia";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $start_datetime, $end_datetime);
            $stmt->execute();
            $result = $stmt->get_result();

            $response = ['labels' => [], 'data' => []];

            while ($row = $result->fetch_assoc()) {
                $response['labels'][] = $row['dia'];
                $response['data'][] = intval($row['total']);
            }   
            break;
    
        case 'dia':
            $fecha = $_GET['fecha'];
    
            // Datos para el gráfico de líneas (por hora)
            $sql = "SELECT HOUR(Fecha_Hora) as hora, SUM(Uso) as total
                    FROM uso_escalera
                    WHERE DATE(Fecha_Hora) = ?
                    GROUP BY hora ORDER BY hora";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $fecha);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
            while ($row = $result->fetch_assoc()) {
                $horas[intval($row['hora'])] = intval($row['total']);
            }
    
            foreach ($horas as $hora => $total) {
                $response['labels'][] = str_pad($hora, 2, "0", STR_PAD_LEFT) . ":00";
                $response['data'][] = $total;
            }
    
            // Extra opcional: puedes también incluir el total por día en otro campo si lo necesitas
            break;
    }
}
    echo json_encode($response);
?>