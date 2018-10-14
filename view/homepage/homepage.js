$(document).ready(function()
{
    $.getJSON('http://mindicador.cl/api', function(data) {
        var dailyIndicators = data;
        $("#uf").html(dailyIndicators.uf.valor);
        $("#dolar").html(dailyIndicators.dolar.valor);
    }).fail(function() {
        console.log('Error al consumir la API!');
    });
});