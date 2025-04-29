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
            label: 'Uso por día',
            data: [],
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }]
    },
    options: {
        onClick: (evt, item) => {

            if (item.length > 0) {
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

function actualizarBarrasConFiltro() {
    const tipo = $('#filtro-tipo').val();
    let url = 'consultar_filtro.php?tipo=' + tipo;

    if (tipo === 'año') {
        url += '&año=' + $('#filtro-año').val();
        titulo.textContent = "Datos del año: " + $('#filtro-año').val();
    } else if (tipo === 'mes') {
        url += '&mes=' + $('#filtro-mes').val();
        titulo.textContent = "Datos del mes: " + $('#filtro-mes').val();
    } else if (tipo === 'semana') {
        url += '&semana=' + $('#filtro-semana').val();
        titulo.textContent = "Datos de la: " + $('#filtro-semana').val();
    } else if (tipo === 'dia') {
        url += '&fecha=' + $('#filtro-dia').val();
        titulo.textContent = "Datos de la : " + $('#filtro-dia').val();
    }

    $('#loader').show();

    $.getJSON(url, function(response) {
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

function ActualizarDatosTotales(selectBarra){
    const tipo = $('#filtro-tipo').val();
    let url = 'test.php?tipo=' + tipo;
    $('#loader').show();
    if (tipo === 'año') {
        url += '&año=' + $('#filtro-año').val() + '&mes=' + selectBarra;
        console.log(url);
    } else if (tipo === 'mes') {
        url += '&mes=' + $('#filtro-mes').val()+ '&semana=' + selectBarra;;
        console.log(selectBarra);
    } else if (tipo === 'semana') {
        url += '&dia=' + selectBarra;
        console.log(selectBarra);
    }
    
    $.getJSON(url, function(response) {
        $('#loader').hide();
        console.log(response);

        totalSuma.textContent = response.total.toLocaleString('es-CL');
        promedio.textContent = response.promedio.toLocaleString('es-CL', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

        lineChart.data.labels = response.labels;
        lineChart.data.datasets[0].data = response.data;
        lineChart.update();

    });

}


$(document).ready(function () {
    const añoActual = new Date().getFullYear();
    for (let y = 2025; y <= añoActual; y++) {
        $('#filtro-año').append(`<option value="${y}">${y}</option>`);
    }

    actualizarBarrasConFiltro();
});

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

function borrarFiltros() {
    const añoActual = new Date().getFullYear();
    $('#filtro-mes, #filtro-semana, #filtro-dia').val('').hide();
    $('#filtro-tipo').val('año')
    $('#filtro-año').val(añoActual).show();
    $('#loader').show();
    titulo.textContent = "Datos del año: " + añoActual;
    console.log(añoActual);
    $.getJSON('consultaDeInicio.php?tipo=año&año=' + añoActual, function(response) {
        $('#loader').hide();
        console.log(response);

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
