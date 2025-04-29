<?php
if (isset($_GET['tipo'])) {
    include 'conexion.php';

    $tipo = $_GET['tipo'];
    $response = ['labels' => [], 'data' => []];

    switch ($tipo) {
        case 'año':          
            $año = intval($_GET['año']);
            $sql = "SELECT SUM(Uso) AS total_uso
                    FROM uso_escalera
                    WHERE YEAR(Fecha_Hora) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $año);
            $stmt->execute();
            $result = $stmt->get_result();
           
            
            break;
    
        case 'mes':
            $año = intval($_GET['mes']);
            $sql = "SELECT SUM(Uso) AS total_uso
                    FROM uso_escalera
                    WHERE MONTH(Fecha_Hora) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $año);
            $stmt->execute();
            $result = $stmt->get_result();
            
            break;
    
        case 'semana':

            break;
    
        case 'dia':
            
            // Extra opcional: puedes también incluir el total por día en otro campo si lo necesitas
            break;
    }
}
    echo json_encode($response);
?>
