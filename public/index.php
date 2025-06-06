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
    <h1 id="titulo"><!-- Aquí se ira cambiando el titulo dinámicamente con JavaScript --></h1>
    <div id="grid">
        <!-- Primera parte, DIV1 Total de uso de la escalera y promedio de uso -->
        <div id="div1">
            <div class="estadisticas-container">
                <div class="estadistica">
                    <h2>Uso total hasta ahora de la escalera:</h2>
                    <p class="valor-grande" id="totalSuma"><!-- se cambiaran los valores dinámicamente con JavaScript --></p>
                    <small>Suma de Uso</small>
                </div>
                <div class="estadistica">
                    <h2>Promedio de pasos de los<br>usuarios por día:</h2>
                    <p class="valor-grande" id="promedio"><!-- se cambiaran los valores dinámicamente con JavaScript --></p>
                    <small>Promedio de Uso</small>
                </div>
            </div>
        </div>
        <!-- Segunda parte, DIV2 Monitoreo de los sensores -->
        <div id="div2">
            <h1>Sensores</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tabla-sensores">
                    <!-- Aquí se insertarán las filas dinámicamente con JavaScript -->
                </tbody>
            </table>
        </div> 
        <!-- tercera parte, DIV3 Graficos del uso de las escaleras -->
        <div id="div3">
            <h2>Uso de Escalera</h2>
            <div class="filtros"><!-- todos los botones/opciones para los distintos filtros -->
                <select id="filtro-tipo" onchange="cambiarFiltrosVisibles()">
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
            <!-- este se mostrara cuando se este cargando los datos y se escondera cuando ya se obtienen los datos -->
            <div id="loader">Cargando datos...</div>
            <!-- contenedor que tendra el gráfico de barras y de lineas -->
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
            <br>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>

</html>