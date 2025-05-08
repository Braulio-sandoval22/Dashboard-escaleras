// Para todas las consultas a la base de datos, se realizaron usando AJAX pero el formate simple de JSON!!

// obtenemos todo aquello que se ira cambiando dinamicamente
const ctxBar = document.getElementById('barChart').getContext('2d');
const ctxLine = document.getElementById('lineChart').getContext('2d');
const totalSuma = document.getElementById('totalSuma');
const promedio = document.getElementById('promedio');
const titulo = document.getElementById('titulo');

const barChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Uso de la escalera',
            data: [],
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }]
    },
    options: {
        onClick: (evt, item) => {
            const tipo = $('#filtro-tipo').val();
            //le damos la condiccion de que podrá hacer click en las barras siempre y cuando no este seleccionado el filtro dia
            if (item.length > 0 && tipo != 'dia') {  
                const selectBarra = barChart.data.labels[item[0].index];
                ActualizarDatosTotales(selectBarra);
            }
        }
    }
});

const lineChart = new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Uso por hora',
            data: [],
            borderColor: 'rgba(255, 99, 132, 1)',
            fill: false
        }]
    }
});

$(document).ready(function () { //Una funcion que se cargara apenas se inicie la pagina
    const añoActual = new Date().getFullYear(); //obtenemos el año actual 
    for (let y = 2025; y <= añoActual; y++) { // desde 2025 hasta el año actual solo saldran los años para filtrar
        $('#filtro-año').append(`<option value="${y}">${y}</option>`);
    }
    cargaPorDefecto(); // se llama a esta función para que cargue los datos por defecto, que seria el año actual
    cargarEstadoSensores(); // se carga la tabla de los sensores
    setInterval(cargarEstadoSensores, 30000);// Luego actualiza cada 30 segundos (30000 ms) la tabla de los sensores
});
// función para cargar los datos segun el filtro ingresado
function actualizarBarrasConFiltro() {
    const tipo = $('#filtro-tipo').val();
    let url = '../app/controllers/consulta_filtro_controller.php?tipo=' + tipo;

    if (tipo === 'año') {
        url += '&año=' + $('#filtro-año').val();
        titulo.textContent = "Datos del año " + $('#filtro-año').val();
    } else if (tipo === 'mes') {
        url += '&mes=' + $('#filtro-mes').val();
        titulo.textContent = "Datos de " + formatearMesAño($('#filtro-mes').val());
    } else if (tipo === 'semana') {
        url += '&semana=' + $('#filtro-semana').val();
        titulo.textContent = formatearSemanaISO($('#filtro-semana').val());
    } else if (tipo === 'dia') {
        url += '&fecha=' + $('#filtro-dia').val();
        titulo.textContent = formatearFechaDia($('#filtro-dia').val());
    }

    $('#loader').show();

    $.getJSON(url, function(response) {
        $('#loader').hide();
        totalSuma.textContent = response.total.toLocaleString('es-CL');
        promedio.textContent = response.promedio.toLocaleString('es-CL', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        barChart.data.labels = response.labels;
        barChart.data.datasets[0].data = response.data;
        barChart.update();

        lineChart.data.labels = response.labelsLine;
        lineChart.data.datasets[0].data = response.dataLine;
        lineChart.update();

    }).fail(function (jqxhr, textStatus, error) {
        console.error("Error al obtener los datos: ", error);
    });
}
// función para cargar los datos segun la barra que se hizo click
function ActualizarDatosTotales(selectBarra){
    const tipo = $('#filtro-tipo').val();
    let url = '../app/controllers/consulta_click_controller.php?tipo=' + tipo;

    if (tipo === 'año') {
        url += '&año=' + $('#filtro-año').val() + '&mes=' + selectBarra;
        titulo.textContent = `Datos de ${selectBarra} del ${$('#filtro-año').val()} `;
    } else if (tipo === 'mes') {
        url += '&mes=' + $('#filtro-mes').val()+ '&semana=' + selectBarra;
        titulo.textContent = `Datos de la ${selectBarra} de ` +  formatearMesAño($('#filtro-mes').val());
    } else if (tipo === 'semana') {
        url += '&dia=' + selectBarra;
        titulo.textContent = formatearFechaDia(selectBarra);
    }

    $('#loader').show();

    $.getJSON(url, function(response) {
        $('#loader').hide();

        totalSuma.textContent = response.total.toLocaleString('es-CL');
        promedio.textContent = response.promedio.toLocaleString('es-CL', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

        lineChart.data.labels = response.labels;
        lineChart.data.datasets[0].data = response.data;
        lineChart.update();
    }).fail(function (jqxhr, textStatus, error) {
        console.error("Error al obtener los datos: ", error);
    });

}
/* función para ir mostando las opciones segun el filtro ingresado, 
   ej: se ingreso filtro año, se mostrara las opciones de años solamente y no mes o semana... */
function cambiarFiltrosVisibles() {
    const tipo = $('#filtro-tipo').val();

    $('#filtro-año, #filtro-mes, #filtro-semana, #filtro-dia').hide();

    if (tipo === 'año') {
        $('#filtro-año').show();
    } else if (tipo === 'mes') {
        $('#filtro-mes').show();
    } else if (tipo === 'semana') {
        $('#filtro-semana').show();
    } else if (tipo === 'dia') {
        $('#filtro-dia').show();
    }
}
// función para simplemente borrar todos los filtros y volver a por defecto
function borrarFiltros() {
    $('#filtro-mes, #filtro-semana, #filtro-dia').val('').hide();
    cargaPorDefecto();
}
// función para cargar los datos por defecto, datos del año actual
function cargaPorDefecto() {
    const añoActual = new Date().getFullYear();
    $('#filtro-tipo').val('año')
    $('#filtro-año').val(añoActual).show();
    $('#loader').show();
    titulo.textContent = "Datos del año " + añoActual;

    $.getJSON('../app/controllers/consulta_filtro_controller.php?tipo=año&año=' + añoActual, function(response) {
        $('#loader').hide();
        totalSuma.textContent = response.total.toLocaleString('es-CL');
        promedio.textContent = response.promedio.toLocaleString('es-CL', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

        barChart.data.labels = response.labels;
        barChart.data.datasets[0].data = response.data;
        barChart.update();

        lineChart.data.labels = response.labelsLine;
        lineChart.data.datasets[0].data = response.dataLine;
        lineChart.update();
    });
}
// función para cargar la tabla de los sensores
function cargarEstadoSensores(){
    $.getJSON('../app/controllers/consulta_sensores_controller.php', function (data) {
        const tbody = $('#tabla-sensores');
        tbody.empty(); // Limpiar antes de insertar

        data.forEach(function (sensores) {
            const estadoTexto = sensores.estado == 1 ? 'Activo' : 'Inactivo';
            const colorClass = sensores.estado == 1 ? 'activo' : 'inactivo';

            const fila = `
                        <tr>
                            <td>${sensores.nombre}</td>
                            <td><div class='estado'><span class='dot ${colorClass}'></span> ${estadoTexto}</div></td>
                        </tr>
                        `;
            tbody.append(fila);
        });
    }).fail(function (jqxhr, textStatus, error) {
        console.error("Error al obtener sensores:", error);
    });
}
// función para cambiar formato de filtro Mes, ej: en vez de 2025-4, sea: abril del 2025
function formatearMesAño(fechaStr) {
    const [año, mes] = fechaStr.split('-');
    
    const nombresMeses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];

    const nombreMes = nombresMeses[parseInt(mes, 10) - 1];

    return `${nombreMes} del ${año}`;
}
// función para cambiar formato de filtro semana, ej: en vez de 2025-W15, sea: Datos de la semana 15 del 2025
function formatearSemanaISO(valor) {
    const [anio, semanaStr] = valor.split('-W');
    const semana = parseInt(semanaStr, 10);
    return `Datos de la Semana ${semana} del ${anio}`;
}
// función para cambiar formato de filtro dia, ej: en vez de 2025-4-14, sea:  Datos del 14 de abril 2025
function formatearFechaDia(valor) {
    const [año, mes, dia] = valor.split('-');

    const nombresMeses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];

    const nombreMes = nombresMeses[parseInt(mes, 10) - 1];
    return `Datos del ${parseInt(dia, 10)} de ${nombreMes} ${año}`;
}