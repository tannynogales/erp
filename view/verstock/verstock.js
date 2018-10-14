/* global userID, bootbox */

$(document).ready(function()
{
    $( "tbody tr" ).click(function() {
        
        var conteo_fecha = $( this ).attr("conteo_fecha");
        var bodega_id    = $( this ).attr("bodega_id");
        var producto_id  = $( this ).attr("producto_id");
        
        window.location.href = "?controller=inventario&action=verstockdetalle&conteo_fecha="+conteo_fecha+"&bodega_id="+bodega_id+"&producto_id="+producto_id; 
        
    });
});