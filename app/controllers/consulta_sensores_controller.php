<?php
    require_once '../config/conexion.php';
    require_once '../models/consultaEstadoSensores.php';

    $modelo = new consultaEstadoSensores($conn);
    $datos = $modelo->obtenerEstadosSensores();  
    header('Content-Type: application/json');
    echo json_encode($datos); 
?>