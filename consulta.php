<?php
include 'conexion.php';

$sqlTotal = "SELECT SUM(Uso) as total FROM uso_escalera";
$sqlPromedio = "SELECT AVG(uso_por_dia) as promedio FROM (
                    SELECT DATE(Fecha_Hora) as dia, SUM(Uso) as uso_por_dia
                    FROM uso_escalera
                    GROUP BY dia
                ) as subquery";

$total = 0;
$promedio = 0;

if ($res = $conn->query($sqlTotal)) {
    $row = $res->fetch_assoc();
    $total = $row['total'];
}

if ($res = $conn->query($sqlPromedio)) {
    $row = $res->fetch_assoc();
    $promedio = $row['promedio'];
}
?>