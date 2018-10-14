/* global userID, bootbox */

$(document).ready(function()
{
    
    $( ".glyphicon.glyphicon-remove-circle" ).click(function() 
    {
        
        var id     = $(this).attr("filaID");
        var tr     = $('tr[filaID="'+id+'"]');
        var nombre = $(tr).find("td[campo='caja_usuario_nombre']").html();
        
        var pregunta = '¿Está seguro que desea <strong>cerrar</strong> la caja de '+nombre+'?';
        
        bootbox.confirm({
            title: "ELIMINAR MOVIMIENTO",
            message: pregunta,
            buttons: 
            {
                cancel: 
                {
                    label: 'Cancelar'
                },
                confirm: 
                {
                    label: 'ELIMINAR'
                }
            },
            callback: function (result) 
            {
                console.log('This was logged in the callback: ' + result);
                if(result)
                {
                    $.ajax
                    ({
                        url: '?controller=Caja_movimiento&action=eliminar',
                        type: 'POST',
                        data:
                            'id='+id,
                        dataType: "json",
                        async:false,
                        success: function(data, textStatus)
                        {
                            //alert(data);
                            //$('#myModal').modal('hide');
                            //alert(data.message);
                            //var tr = $('tr[ventaID="'+id+'"]');
                            $(tr).remove();
                            bootbox.alert
                            ({
                                message: data["message"],
                                size:    'small'
                                //className: 'bb-alternate-modal',
                                // backdrop: true, // Dismiss with overlay click                    
                            });
                        }		
                    });                    
                }
            }
        });        
    });
    
});