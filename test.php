<?php
    include 'conexion.php';

    $response = 0;
    
    $año = intval($_GET['mes']);
    $sql = "SELECT SUM(Uso) AS total_uso
            FROM uso_escalera
            WHERE MONTH(Fecha_Hora) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $año);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode($result)
?>