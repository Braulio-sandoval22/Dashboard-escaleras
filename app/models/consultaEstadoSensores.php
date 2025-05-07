<?php
class consultaEstadoSensores {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function obtenerEstadosSensores() {
        $query = "SELECT * FROM sensores";
        $result = $this->conn->query($query);

        $sensores = [];

        while ($row = $result->fetch_assoc()) {
            $sensores[] = [
                'nombre' => $row['nombre'],
                'estado' => (int)$row['estado'] // convertir a int para evitar problemas de tipo
            ];
        }

        return $sensores;
    }
}
?>
