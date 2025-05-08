<?php
    require_once '../config/conexion.php';
    // Consultas de los clicks
    require_once '../models/consultaClickAño.php';
    require_once '../models/consultaClickMes.php';
    require_once '../models/consultaClickSemana.php';

    if (isset($_GET['tipo'])) {
        $tipo = $_GET['tipo'];

        switch ($tipo) {
            case 'año':
                $año = intval($_GET['año']);
                $mes = $_GET['mes'];
                $modelo = new consultaClickAño($conn);
                $datos = $modelo->obtenerDatosPorAño($año,$mes);     
                break;
            
            case 'mes':
                $mesSeleccionado = $_GET['mes']; // Ejemplo: "2025-04"
                $semana = $_GET['semana'];
                $modelo = new consultaClickMes($conn);
                $datos = $modelo->obtenerDatosPorMes($mesSeleccionado,$semana);  
                break;           
                
            case 'semana':
                $dia = $_GET['dia'];
                $modelo = new consultaClickSemana($conn);
                $datos = $modelo->obtenerDatosPorSemana($dia);  
                break;  
        }
        header('Content-Type: application/json');
        echo json_encode($datos); 
    }
?>