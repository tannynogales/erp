/* global userID, bootbox */

$(document).ready(function()
{
    //$("#myModal").modal('show');
    
    $( ".glyphicon.glyphicon-pencil" ).click(function() 
    {   
        var userID = $(this).attr("userID");
        var tr = $('tr[userID="'+userID+'"]');
        
        var rut    = $(tr).find("td[campo='rut']").html();
        var nombre = $(tr).find("td[campo='nombre']").html();
        
        bootbox.confirm({
            title: "EDITAR USUARIO",
            message: "¿Está seguro que desea <strong>editar</strong> el usuario "+nombre+", Rut: "+rut+"?",
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
                    window.location.href = "?controller=usuarios&action=editar&rut_busca="+rut;                     
                }
            }
        });
    
    });
    
    $( ".glyphicon.glyphicon-remove" ).click(function() 
    {
        
        var userID = $(this).attr("userID");
        var tr = $('tr[userID="'+userID+'"]');
        
        var rut    = $(tr).find("td[campo='rut']").html();
        var nombre = $(tr).find("td[campo='nombre']").html();
        
        var pregunta = "¿Está seguro que desea <strong>eliminar</strong> el usuario "+nombre+", Rut: "+rut+"?";
        
        bootbox.confirm({
            title: "ELIMINAR USUARIO",
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
                        url: '?controller=usuarios&action=eliminar',
                        type: 'POST',
                        data:
                            'id='+userID,
                        dataType: "json",
                        async:false,
                        success: function(data, textStatus)
                        {
                            $('#myModal').modal('hide');
                            //alert(data.message);
                            var tr = $('tr[userID="'+userID+'"]');
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

function send_data_post()
{
    // Creamos el formulario auxiliar
    var form = document.createElement( "form" );

    // Le añadimos atributos como el name, action y el method
    form.setAttribute( "name", "formulario" );
    form.setAttribute( "action", "" );
    form.setAttribute( "method", "post" );

    // Creamos un input para enviar el valor
    var input = document.createElement( "input" );

    // Le añadimos atributos como el name, type y el value
    input.setAttribute( "name", "prueba" );
    input.setAttribute( "type", "hidden" );
    input.setAttribute( "value", "Soy una prueba" );

    // Añadimos el input al formulario
    form.appendChild( input );

    // Añadimos el formulario al documento
    document.getElementsByTagName( "body" )[0].appendChild( form );

    // Hacemos submit
    document.formulario.submit();    
}
