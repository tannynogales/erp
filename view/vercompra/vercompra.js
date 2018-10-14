/* global userID, bootbox */

$(document).ready(function()
{
    
    $( ".glyphicon.glyphicon-pencil" ).click(function() 
    {   
        var filaID = $(this).attr("filaID");
        //var tr     = $('tr[filaID="'+filaID+'"]');
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        
        bootbox.confirm({
            title: "EDITAR COMPRA",
            message: "¿Está seguro que desea <strong>editar</strong> la Compra N° "+filaID+"?",
            buttons: 
            {
                cancel: 
                {
                    label: 'Cancelar'
                },
                confirm: 
                {
                    label: 'EDITAR'
                }
            },
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);
                if(result)
                {
                    window.location.href = "?controller=compra&action=editar&id_busca="+filaID;                     
                }
            }
        });
    
    });
    
    $( ".glyphicon.glyphicon-remove" ).click(function() 
    {
        
        var id = $(this).attr("filaID");
        var tr = $('tr[filaID="'+id+'"]');
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        
        var pregunta = '¿Está seguro que desea <strong>eliminar</strong> la compra "'+id+'"?';
        
        bootbox.confirm({
            title: "ELIMINAR COMPRA",
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
                        url: '?controller=compra&action=eliminar',
                        type: 'POST',
                        data:
                            'id='+id,
                        dataType: "json",
                        async:false,
                        success: function(data, textStatus)
                        {
                            //alert(data);
                            $('#myModal').modal('hide');
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