/* global userID, bootbox */

$(document).ready(function()
{   
    $( "#limpiar_filtros" ).click(function() 
    {
        $("#tabla_productos tbody tr").show();

        $("#filtrar_productos").val("");
        $("#select_validado").val("por_defecto");
        
    });    
    
    $( "#select_validado" ).change(function() 
    {
        filtrar();
    }); 
    $("#filtrar_productos").on("keyup", function(e) 
    {
        filtrar();
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


function filtrar()
{
    var coincidencias;
    var coincidencia;
    var pajar;
    var exp;

    var filtros =  new Array(); 
     
    if ( $( "#select_validado" ).val() != "por_defecto" )
        filtros.push( [$( "#select_validado" ).val(), $( "#select_validado" ).attr("filtrar_columna")] );

    if ( $( "#filtrar_productos" ).val() != "" )
        filtros.push( [$( "#filtrar_productos" ).val(), $( "#filtrar_productos" ).attr("filtrar_columna")] );
    
    //alert(filtros.length);
    
    var coincidencia = true;
    
    $("#tabla_productos tbody tr").each(function()
    {
        
        coincidencias = true;
        for (i = 0; i < filtros.length; i++) 
        {
            if(  filtros[i][1] == "todas"  )
                pajar = $(this).html();
            else
                pajar = $(this).find('td[campo="'+filtros[i][1]+'"]').html();
            
            exp = new RegExp(filtros[i][0], 'gi');
            //alert('pajar: '+pajar+', exp: '+exp);
            coincidencia = pajar.match(exp);
            if (coincidencia==null)
                coincidencias = false;
        }

        if (coincidencias==true)
            $(this).show();
        else
            $(this).hide();
    });

    console.log(filtros);

}