/* global userID, bootbox */

$(document).ready(function()
{
    $( "#fecha_desde, #fecha_hasta" ).datepicker
    ({
        dateFormat: "dd/mm/yy",
        //minDate: "-19D", 
        //maxDate: "+1M +1D",
        changeMonth: true,
        changeYear: false        
    });
    
$("#filtro_identificador").keypress(function(e) {
    if(e.which == 13) 
    {
        var identificador = $(this).val();
        identificador = identificador.toUpperCase();
        if(identificador != "")
        {
            $( "#tabla_ventas tbody tr").each(function( index ) {
                aux = $( this ).find("td").eq(1).html();
                if (aux == identificador)
                    $( this ).show();
                else
                    $( this ).hide();
            });            
            //$("tr[validado=1]").hide();
        }
        else
            $( "#tabla_ventas tbody tr").show();
        
    }
});

$('#filtro_rut').rutFormat();

$("#filtro_rut").keypress(function(e) {
    if(e.which == 13) 
    {
        var identificador = $(this).val();
        //identificador = identificador.toUpperCase();
        identificador = $.Rut.quitarFormato(identificador);
        identificador = identificador.substring(0,identificador.length-1);
        
        //alert(identificador);
        if(identificador != "")
        {
            $( "#tabla_ventas tbody tr").each(function( index ) {
                aux = $( this ).attr("cliente");
                if (aux == identificador)
                    $( this ).show();
                else
                    $( this ).hide();
            });            
            //$("tr[validado=1]").hide();
        }
        else
            $( "#tabla_ventas tbody tr").show();
        
    }
});
    $( "#tabla_ventas" ).on( "click", ".glyphicon.glyphicon-ok[validado=0]", function()
        
    //$( ".glyphicon.glyphicon-ok[validado=0]" ).click(function() 
    {   
        if ( $(this).attr("validado") == 0)
        {     
            var filaID     = $(this).attr("filaID");
            var fuente     = $("#"+filaID+"").attr("fuente");
            var VentaID    = $("#"+filaID+"").attr("VentaID");

            //alert(VentaID+", "+fuente);
            //var tr     = $("tr[filaID="+filaID+"][fuente="+fuente+"]");
            var tr         = $("#"+filaID+"");
            var tipo_venta = $(tr).find("td[campo='tipo_venta']").html();

            var pregunta = '¿Está seguro que desea <strong>aprobar</strong> la venta ('+tipo_venta+') "'+VentaID+'"?';

            bootbox.confirm({
                title: "APROBAR VENTA",
                message: pregunta,
                buttons: 
                {
                    cancel: 
                    {
                        label: 'Cancelar'
                    },
                    confirm: 
                    {
                        label: 'APROBAR'
                    }
                },
                callback: function (result) 
                {
                    //console.log('This was logged in the callback: ' + result);
                    if(result)
                    {
                        $.ajax
                        ({
                            url: '?controller=factura&action=validar',
                            type: 'POST',
                            data:
                                'id='+VentaID+"&"+
                                'fuente='+fuente,
                            dataType: "json",
                            async:false,
                            success: function(data, textStatus)
                            {
                                //alert(data);
                                //$('#myModal').modal('hide');
                                //alert(data.message);
                                //var tr = $('tr[ventaID="'+id+'"]');
                                $(tr).attr("validado", 1);
                                    //$(this).attr("validado", 1);
                                $(tr).find("span.glyphicon-ok").attr("validado", "1")
                                $(this).attr("validado", 1);
                                $(tr).find("span.glyphicon-pencil").hide();
                                $(tr).find("span.glyphicon-remove").hide();
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
    }
    });

    $( ".glyphicon.glyphicon-eye-open" ).click(function() 
    {   
        var filaID = $(this).attr("filaID");
        //var fuente = $("tr[filaID="+filaID+"]").attr("fuente");
        var fuente = $("#"+filaID+"").attr("fuente");
        var VentaID = $("#"+filaID+"").attr("VentaID");
        //alert(filaID+", "+fuente);
        //bootbox.alert("Aún no está habilitada la visualización del detalle de las facturas electrónicas");
        var dialog = bootbox.dialog({
            title: 'VER DETALLE'+' ('+fuente+': '+VentaID+')',
            message: '<p><img src="img/spin.gif" style="width:20px;height:20px;"></p>'
        });
        
        dialog.init(function()
        {
            $.ajax
            ({
                url: '?controller=factura&action=VerDetalle',
                type: 'POST',
                data:
                    'id='+VentaID+"&"+
                    'fuente='+fuente,
                dataType: "json",
                async:false,
                success: function(data, textStatus)
                {
                    //alert(data);
                    dialog.find('.bootbox-body').html(data["html"]);
                },		
                error: function (request, status, error) 
                {
                    alert("Ha ocurrido un error:\n"+request.responseText);
                }                
            });  
        });

    });
    
    $( "#button_filtrar_pendiente" ).click(function() 
    {
        $("tr[validado=1]").hide();
        
        sin_validar = $("tr[validado=0]").length;
        
        if(sin_validar==0)
            bootbox.alert("Todas las ventas están Aprobadas");
        
    }); 
    
    $( ".glyphicon.glyphicon-pencil" ).click(function() 
    {   
        var filaID = $(this).attr("filaID");
        var VentaID = $("#"+filaID+"").attr("VentaID");
        //var tr     = $('tr[filaID="'+filaID+'"]');
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        //var nombre = $(tr).find("td[campo='nombre']").html();
        
        bootbox.confirm({
            title: "EDITAR VENTA",
            message: "¿Está seguro que desea <strong>editar</strong> la Venta N° "+VentaID+"?",
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
                    window.location.href = "?controller=factura&action=editar&id_busca="+VentaID;                     
                }
            }
        });
    
    });
    
    $( ".glyphicon.glyphicon-remove" ).click(function() 
    {
        var filaID = $(this).attr("filaID");
        var id = $("#"+filaID+"").attr("VentaID");
        var tr = $("#"+filaID+"");
        
        //var rut    = $(tr).find("td[campo='rut']").html();
        //var nombre = $(tr).find("td[campo='nombre']").html();
        
        var pregunta = '¿Está seguro que desea <strong>eliminar</strong> la venta "'+id+'"?';
        
        bootbox.confirm({
            title: "ELIMINAR VENTA",
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
                        url: '?controller=factura&action=eliminar',
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
