/* global userID, bootbox */

$(document).ready(function()
{   

    $("#filtrar_productos").on("keyup", function(e) 
    {
        cadena = $(this).val();
        //alert(valor_a_buscar);
        
        $("#tabla_productos tbody tr").each(function()
        {
            contenido_fila = $(this).html();
            exp = new RegExp(cadena, 'gi'); 
            coincidencias = contenido_fila.match(exp);
               if (coincidencias!=null)
                   $(this).show();
               else
                   $(this).hide();
        });
    });    
    
    $( ".glyphicon.glyphicon-pencil" ).click(function() 
    {   
        //var filaID = $(this).attr("filaID");
        var codigo = $(this).attr("codigo");
        //var tr     = $('tr[filaID="'+filaID+'"]');
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        //var nombre = $(tr).find("td[campo='nombre']").html();
        
        bootbox.confirm({
            title: "EDITAR PRODUCTO",
            message: "¿Está seguro que desea <strong>editar</strong> el Producto código "+codigo+"?",
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
                    window.location.href = "?controller=producto&action=editar&id_busca="+codigo;                     
                }
            }
        });
    
    });
    
    $( ".glyphicon.glyphicon-remove" ).click(function() 
    {
        
        var id     = $(this).attr("filaID");
        var codigo = $(this).attr("codigo");
        var tr = $('tr[filaID="'+id+'"]');
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        //var nombre = $(tr).find("td[campo='nombre']").html();
        
        var pregunta = '¿Está seguro que desea <strong>eliminar</strong> el Producto "'+codigo+'"?';
        
        bootbox.confirm({
            title: "ELIMINAR PRODUCTO",
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
                        url: '?controller=producto&action=eliminar',
                        type: 'POST',
                        data:
                            'id='+id+
                            '&codigo='+codigo,
                        dataType: "json",
                        async:false,
                        success: function(data, textStatus)
                        {
                            //alert(data);
                            console.log(data);
                            if(data["result"])
                            {
                                $(tr).remove();
                            }
                            //alert("Función no Habilitada");
                            //return true;
                            $('#myModal').modal('hide');
                            bootbox.alert
                            ({
                                message: data["message"],
                                size:    'small'
                                //className: 'bb-alternate-modal',
                                // backdrop: true, // Dismiss with overlay click                    
                            });
                        },
                        error: function (request, status, error) 
                        {
                            alert(request.responseText);
                        }                        
                    });                    
                }
            }
        });        
    });
    
});
