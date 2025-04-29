const ctxBar = document.getElementById('barChart').getContext('2d');
const ctxLine = document.getElementById('lineChart').getContext('2d');
const totalSuma = document.getElementById('.totalSuma');
const promedio = document.querySelector('.promedio');


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
    } else if (tipo === 'mes') {
        url += '&mes=' + $('#filtro-mes').val();
    } else if (tipo === 'semana') {
        url += '&semana=' + $('#filtro-semana').val();
    } else if (tipo === 'dia') {
        url += '&fecha=' + $('#filtro-dia').val();
    }

    $('#loader').show();

    $.getJSON(url, function(response) {
        $('#loader').hide();

        barChart.data.labels = response.labels;
        barChart.data.datasets[0].data = response.data;
        barChart.update();

        lineChart.data.labels = response.labels;
        lineChart.data.datasets[0].data = response.data;
        lineChart.update();

    });
}

function ActualizarDatosTotales(selectBarra){
    const tipo = $('#filtro-tipo').val();
    let url = 'test.php?tipo=' + tipo;
    
    if (tipo === 'año') {
        console.log(selectBarra);  
        url += '&mes=' + selectBarra;
    } else if (tipo === 'mes') {
        console.log(selectBarra);
    } else if (tipo === 'semana') {
        console.log(selectBarra);
    } else if (tipo === 'dia') {
        console.log(selectBarra);
    }
    
    $.getJSON(url, function(response) {
        
        totalSuma.textContent($response);

    });
    // $.getJSON('actualizarDatos.php?tipo=' + tipo, function(response) {

    //     if (tipo === 'año') {
    //         url += '&mes=' + selectBarra;
    //         // url += '&año=' + $('#filtro-año').val();
    //     } else if (tipo === 'mes') {
    //         url += '&mes=' + $('#filtro-mes').val();
    //     } else if (tipo === 'semana') {
    //         url += '&semana=' + $('#filtro-semana').val();
    //     } else if (tipo === 'dia') {
    //         url += '&fecha=' + $('#filtro-dia').val();
    //     }


    //     $('#loader').hide();

    //     totalSuma.textContent($result);


    // });

}

function cargarUsoPorHora(fecha) {
    $('#loader').show();
    $.getJSON('consultar_filtro.php?tipo=dia&fecha=' + fecha, function(response) {
        $('#loader').hide();
        lineChart.data.labels = response.labels;
        lineChart.data.datasets[0].data = response.data;
        lineChart.update();
    });
}

$(document).ready(function () {
    const añoActual = new Date().getFullYear();
    for (let y = 2024; y <= añoActual; y++) {
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
    $('#filtro-año, #filtro-mes, #filtro-semana, #filtro-dia').val('');
    barChart.data.labels = [];
    barChart.data.datasets[0].data = [];
    barChart.update();
    lineChart.data.labels = [];
    lineChart.data.datasets[0].data = [];
    lineChart.update();
}
