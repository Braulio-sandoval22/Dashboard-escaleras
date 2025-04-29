<?php
    include 'conexion.php';

    $tipo = $_GET['tipo'];
    $mes = $_GET['mes'];
    $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];
    $horas = array_fill(0, 24, 0); // Inicializa las 24 horas
    $semana = $_GET['semana'];
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

    switch ($semana) {
        case "Semana 1":
            $numSemana = 1;
            break;
        case "Semana 2":
            $numSemana = 2;
            break;
        case "Semana 3":
            $numSemana = 3;
            break;
        case "Semana 4":
            $numSemana = 4;
            break;
        default: $numSemana = 5;
    }


    switch ($tipo) {
        case 'año':
            $año = intval($_GET['año']);
            $sql = "SELECT MONTH(Fecha_Hora) as mes, SUM(Uso) as total, AVG(Uso) as promedio
                    FROM uso_escalera
                    WHERE YEAR(Fecha_Hora) = ? and MONTH(Fecha_Hora) = ?
                    GROUP BY mes ORDER BY mes";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $año,$numMes);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $sqlHora  = "SELECT HOUR(Fecha_Hora) AS hora, SUM(Uso) AS total
                    FROM uso_escalera
                    WHERE YEAR(Fecha_Hora) = ? AND MONTH(Fecha_Hora) = ?
                    GROUP BY hora
                    ORDER BY hora";
            $stmtHora  = $conn->prepare($sqlHora);
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

            break;
    
        case 'mes':
            $mesSeleccionado = $_GET['mes']; // Ejemplo: "2025-04"
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

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $fechaInicio, $fechaFin,$numSemana);
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
            $stmtHora  = $conn->prepare($sqlHora);
            $stmtHora ->bind_param("sss", $fechaInicio,$fechaFin,$numSemana);
            $stmtHora ->execute();
            $resultHora  = $stmtHora->get_result();

            $response = ['total' => [], 'promedio' => [],'labels' => [], 'data' => []];

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

            break;
    
        case 'semana':
            $dia = $_GET['dia'];

            $sql = "SELECT SUM(Uso) as total, AVG(Uso) as promedio
                    FROM uso_escalera
                    WHERE DATE(Fecha_Hora) =?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $dia);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $sqlHora = "SELECT HOUR(Fecha_Hora) as hora, SUM(Uso) as total
                        FROM uso_escalera
                        WHERE DATE(Fecha_Hora) =?
                        GROUP BY hora ORDER BY hora";
            $stmtHora  = $conn->prepare($sqlHora);
            $stmtHora ->bind_param("i", $dia);
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
            break;
    
        case 'dia':

            break;
    }
    

    echo json_encode($response)
?>