<?php
    require_once '../config/conexion.php';
    // Consultas de los filtros
    require_once '../models/consultaFiltroAño.php';
    require_once '../models/consultaFiltroMes.php';
    require_once '../models/consultaFiltroSemana.php';
    require_once '../models/consultaFiltroDia.php';

    if (isset($_GET['tipo'])) {
        $tipo = $_GET['tipo'];

        switch ($tipo) {
            case 'año':
                $año = intval($_GET['año']);
                $modelo = new consultaFiltroAño($conn);
                $datos = $modelo->obtenerDatosPorAño($año);     
                break;
            
            case 'mes':
                $mesSeleccionado = $_GET['mes']; // Ejemplo: "2025-04"
                $modelo = new consultaFiltroMes($conn);
                $datos = $modelo->obtenerDatosPorMes($mesSeleccionado);  
                break;           
                
            case 'semana':
                $week = $_GET['semana']; // Ejemplo: "2025-W14"
                $modelo = new consultaFiltroSemana($conn);
                $datos = $modelo->obtenerDatosPorSemana($week);  
                break;  

            case 'dia':
                $fecha = $_GET['fecha'];
                $modelo = new consultaFiltroDia($conn);
                $datos = $modelo->obtenerDatosPorDia($fecha);  
                break;  
        }
        header('Content-Type: application/json');
        echo json_encode($datos); 
    }
?>