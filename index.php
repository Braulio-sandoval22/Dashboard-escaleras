<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap para estilos rápidos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <title>Dashboard uso de la escalera</title>
</head>

<body>
    <h1 id="titulo">Titulo XYZ</h1>
    <div id="grid">
        <!--
         Primera parte, DIV 1
         Total de uso de la escalera y promedio de uso
        -->
        <div id="div1">
            <div class="estadisticas-container">
                <div class="estadistica">
                    <h2>Uso total hasta ahora de la escalera:</h2>
                    <p class="valor-grande" id="totalSuma">X</p>
                    <small>Suma de Uso</small>
                </div>
                <div class="estadistica">
                    <h2>Promedio de pasos de los<br>usuarios por día:</h2>
                    <p class="valor-grande" id="promedio">X</p>
                    <small>Promedio de Uso</small>
                </div>
            </div>
        </div>


         <!--
         Segunda parte, DIV 2
         Monitoreo de los sensores
        -->
        <div id="div2">
            <h1>Sensores</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include('conexion.php');
                        $query = "SELECT * FROM sensores";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()) {
                        $colorClass = $row['estado'] ? 'activo' : 'inactivo';
                        $estadoTexto = $row['estado'] ? 'Activo' : 'Inactivo';
                        echo "<tr>";
                        echo "<td>{$row['nombre']}</td>";
                        echo "<td><div class='estado'><span class='dot $colorClass'></span> $estadoTexto</div></td>";
                        echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div> 
         <!--
         tercera parte, DIV 2
         Graficos del uso de las escaleras
        -->
        <div id="div3">
            <h2>Uso de Escalera</h2>

            <div class="filtros">
                <select id="filtro-tipo" onchange="cambiarFiltrosVisibles()">
                    <option value="">Filtrar</option>
                    <option value="año">Por Año</option>
                    <option value="mes">Por Mes</option>
                    <option value="semana">Por Semana</option>
                    <option value="dia">Por Día</option>
                </select>

                <select id="filtro-año" style="display:none;"></select>
                <input type="month" id="filtro-mes" style="display:none;">
                
                <input type="week" id="filtro-semana" style="display:none;">
                
                <input type="date" id="filtro-dia" style="display:none;">
                <br>
                <button id="btnActualizar" onclick="actualizarBarrasConFiltro()">Actualizar</button>
                <button onclick="borrarFiltros()">Borrar Filtros</button>
            </div>

            <div id="loader">Cargando datos...</div>

            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
            <br>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>

            <script src="scripts.js"></script>

        </div>
    </div>

</body>

</html>