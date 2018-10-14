/* global userID, bootbox */

$(document).ready(function()
{
    // Permito gatillar un evento cuando ocupo "hide() // show()"
    (function ($) 
    {
	$.each(['show', 'hide'], function (i, ev) {
            var el = $.fn[ev];
	    $.fn[ev] = function () 
            {
                this.trigger(ev);
                return el.apply(this, arguments);
	    };
        });
    })(jQuery);

    
    panel_cliente();
    
    $('#panel_cliente').on('show', function() 
    {
        $('#panel_cliente').find("input").attr('required', true);
    });

    $('#panel_cliente').on('hide', function() 
    {
        $('#panel_cliente').find("input").attr('required', false);
    });

    //var cliente_visible = $("#panel_cliente").is(":visible");
    //alert(cliente_visible);
    
    $('#cliente_rut').rutFormat();
    
    $('#vuelto, #efectivo').priceFormat
    ({
        prefix: '$ ',
        centsSeparator: '.',
        thousandsSeparator: ',',
        centsLimit: 0,
        allowNegative: false
    });
                        
    //$("#addProductoModal").modal('show');
    $( "#datepicker" ).datepicker
    ({
        dateFormat: "dd/mm/yy",
        minDate: "-19D", 
        maxDate: "+1M +1D",
        changeMonth: false,
        changeYear: false        
    });
    
    $("#datepicker").keydown(function(e)
    {
        e.preventDefault();
    })  
    
    $( "#button_quitarCliente" ).click(function() 
    {
        var tipo_venta = $("#venta_tipo").val();
        if(tipo_venta == 1 )
        {
            bootbox.alert
            ({
                message: "Es obligatorio ingresar el Cliente para Facturas",
                size:    'small',
                className: 'bb-alternate-modal',
                backdrop: true // Dismiss with overlay click                    
            });
        }
        else
        {
            $("#panel_cliente").hide();
            $( "#button_agregarCliente" ).show();
            $( "#button_quitarCliente" ).hide();         
            $('#cliente_check').prop('checked', false);
        }
    });  
    
    $( "#button_agregarCliente" ).click(function() 
    {
        $("#panel_cliente").show();
        $("#button_agregarCliente" ).hide();
        $("#button_quitarCliente" ).show();         
        $('#cliente_check').prop('checked', true);
        $('#cliente_rut').focus();
    });  
    
    $( "#button_addProductoModal" ).click(function() 
    {
        buscar_producto();
    });    
    
    function panel_cliente()
    {
        var venta_tipo = $("#venta_tipo").val();
        //alert(venta_tipo);
        if ( venta_tipo == 1 )
        {
            // si es Factura Electrónica
            $("#panel_cliente").show("slow");
            $('#panel_cliente').find("input").attr('required', true);
            $( "#button_agregarCliente" ).hide();
            $( "#button_quitarCliente" ).show();
            $('#cliente_check').prop('checked', true);
        }
        else
        {
            $("#panel_cliente").hide();
            $('#panel_cliente').find("input").attr('required', false);
            $( "#button_agregarCliente" ).show();
            $( "#button_quitarCliente" ).hide();         
            $('#cliente_check').prop('checked', false);
        }        
    }
    
    $( "#venta_tipo" ).change(function() 
    {
        panel_cliente();
    });

    $( "#cliente_rut" ).keyup(function(e)
    {
        var code = (e.keyCode ? e.keyCode : e.which);
        var es_rut = false;
        
        if( (code >= 48 && code <=57 ) || code == 75 )
        {
            es_rut = true;
        }
    
        var rut = $(this).val();

        if(  $.Rut.validar(rut) && $.Rut.quitarFormato(rut) > 1000000 )
        {
            var controller = "factura";
            var action     = "buscaContribuyente";
            //alert(rut);
            $.ajax
            ({
                url: '?controller='+controller+'&action='+action,
                type: 'POST',
                data:
                    'rut='+rut,
                dataType: "json",
                async:false,
                success: function(data, textStatus)
                {
                    console.log(data);
                    //{"status":{"code":"403","message":"mForbidden"}
                    if( data["status"]["code"] == 200 )
                    {
                        $("#cliente_nombre").val(data["body"]["razon_social"]);
                        $("#cliente_giro").val(data["body"]["giro"]);
                        $("#cliente_direccion").val(data["body"]["direccion"]);
                        $("#cliente_comuna").val(data["body"]["comuna"]);
                        $("#cliente_correo").val(data["body"]["correo"]);
                        $("#cliente_contacto").val(data["body"]["contacto"]);
                    }
                    else
                    {
                        if (es_rut)
                        bootbox.alert
                        ({
                            message: 
                                    "<strong>No Encontramos Resultados</strong><br>"+
                                    data["status"]["message"]+"<br>"+
                                    "Código: "+data["status"]["code"],
                            size:    'small',
                            className: 'bb-alternate-modal',
                            backdrop: true // Dismiss with overlay click                    
                        });                        
                    }
                },
                error: function (request, status, error) 
                {
                    alert("si error: "+request.responseText);
                }            
            });            
        }
    });
    
    //$( "#producto_busca" ).keyup(function() 
    $( "#producto_busca" ).keypress(function(e)
    {
        if(e.which == 13) 
        {
            var controller = "factura";
            var action     = "buscaProducto";

            var valor = $(this).val();

            $.ajax
            ({
                url: '?controller='+controller+'&action='+action,
                type: 'POST',
                data:
                    'valor='+valor,
                dataType: "json",
                async:false,
                success: function(data, textStatus)
                {
                    //alert( "no error: "+data["html"] );
                    $("#table_product_result").find("tbody tr").remove();
                    $("#table_product_result").find("tbody").append(data["html"]);
                    $("#table_product_result").show();
                },
                error: function (request, status, error) 
                {
                    alert("si error: "+request.responseText);
                }            
            });
        }
    });
    
    $( "#efectivo" ).keyup(function() 
    {
        calcula_vuelto();
    });

    function calcula_vuelto()
    {
        var efectivo = $("#efectivo").unmask();
            efectivo = parseInt(efectivo)
        if(isNaN(efectivo))
            efectivo = 0;
        
            //alert(valor);
        var total = $("#resumen_total").unmask();
            total = parseInt(total);
            
        if(isNaN(total))
            total = 0;
        
        if(efectivo >= total)
        {
            var dif = 0;
            dif = efectivo - total;
            $("#vuelto").val(number_format(dif));
        }else
            $("#vuelto").val(number_format(0));        
    }
    
    $("#table_product_result tbody").on("click", "tr", function()
    {
        //console.log( $( this ).text() );
        var prod_id = $(this).attr("prod_id");
        var cod     = $(this).find(":eq(0)").text();
        var nombre  = $(this).find(":eq(1)").text();
        var precio  = $(this).find(":eq(2)").text();
        //alert( $(cod).html() );
        var pregunta = "¿Está seguro que desea agregar el producto <strong>'"+cod+"' - '"+nombre+"'</strong>?";
        
        bootbox.confirm({
            title: "AGREGAR PRODUCTO",
            message: pregunta,
            buttons: 
            {
                cancel: 
                {
                    label: 'Cancelar'
                },
                confirm: 
                {
                    label: 'AGREGAR'
                }
            },
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);
                if(result)
                {
                    var existe = $( "#productoFila_"+prod_id ).length;
                    //alert(prod_id);
                    if( existe === 0 )
                    {
                        var tr = "";
                        tr += '<tr id="productoFila_'+prod_id+'">';
                        tr += ' <td><input campo="id" name="producto_id_'+prod_id+'" type="text" class="form-control" value="'+prod_id+'" readonly></td>';
                        tr += ' <td campo="codigo">'+cod+'</td>';
                        tr += " <td>"+nombre+"</td>";
                        tr += ' <td><input campo="cantidad" name="producto_cantidad_'+prod_id+'" type="text"  class="form-control" value="1"></td>';
                        tr += ' <td><input campo="precio"   name="producto_precio_'+prod_id+'"   type="text"  class="form-control" value="'+precio+'"></td>';
                        tr += ' <td campo="total"></td>';
                        tr += ' <td><span class="glyphicon glyphicon-remove" aria-hidden="true" producto_id="'+prod_id+'"></span></td>';
                       
                        tr += "</tr>";
                        $("#productos_form tbody").append(tr);
                        //http://jquerypriceformat.com/#examples
                        
                        actualizaFila( $("#productoFila_"+prod_id) );
                        
                        $('#productos_form input[name="producto_precio_'+prod_id+'"]').priceFormat
                        ({
                            prefix: '$ ',
                            centsSeparator: '.',
                            thousandsSeparator: ',',
                            centsLimit: 0,
                            allowNegative: false
                        });
                        
                        $('#productos_form input[name="producto_cantidad_'+prod_id+'"]').priceFormat
                        ({
                            prefix: '',
                            centsSeparator: '.',
                            thousandsSeparator: ',',
                            limit: 3,
                            centsLimit: 0,
                            allowNegative: false
                        });
                        $("#addProductoModal").modal('hide');
                        $("#productos_form").show();
                        //window.location.href = "?controller=usuarios&action=editar&rut_busca="+rut;
                    }
                    else
                    {
                        bootbox.alert
                        ({
                            message: "Este producto ya fue ingresado, por favor intente con otro.",
                            size:    'small'
                            //className: 'bb-alternate-modal',
                            // backdrop: true, // Dismiss with overlay click                    
                        });                        
                    }
                }
            }
        });        
    });
    $("#productos_form").on("keyup", 'tbody tr input[campo="precio"], tbody tr input[campo="cantidad"]', function()
    {
        var fila = $(this).parents("tr");
        actualizaFila(fila)
    });
    $('#addProductoModal').on('shown.bs.modal', function () 
    {
        $('#producto_busca').focus();
    });
    
    function actualizaFila(filaObjeto)
    {
        var precio   = $(filaObjeto).find('input[campo="precio"]').val();
        precio       = parseInt (  precio.replace(/\D/g,'') );

        var cantidad = $(filaObjeto).find('input[campo="cantidad"]').val();
        cantidad     = parseInt ( cantidad.replace(/\D/g,'') );

        var total = precio*cantidad;

        $(filaObjeto).find("td[campo=total]").html( number_format(total) );

        actualizaResumen();
    }
    function actualizaResumen()
    {
        var neto = 0;
        var precios   = $('#productos_form td[campo="total"]');
        $( precios ).each(function() 
        {
            var precio = parseInt ($(this).html().replace(/\D/g,'') );
            neto += precio; 
        });

        var iva   = Math.ceil(neto * 0.19);
        var total = neto + iva;

        $("#resumen_iva")  .val( number_format(iva) );
        $("#resumen_neto") .val( number_format(neto) ); 
        $("#resumen_total").val( number_format(total) ); 
        
        calcula_vuelto();
    }
    function number_format(value)
    {
        var decimales = 0;
        //var value  = 724250.223;
        var numero = '$ ' + value.toFixed(decimales).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        return numero;
    }
    $("#productos_form").on("click", ".glyphicon-remove", function()
    {   
        var producto_id = $(this).attr("producto_id");
        //alert(producto_id);
        var tr          = $('#productoFila_'+producto_id);
        //alert( $(tr).html() );
        var codigo      = $(tr).find("td[campo=codigo]").html();
        //alert(codigo);
        
        var pregunta = "¿Está seguro que quitar el producto código <strong>"+codigo+"</strong>?";
        
        bootbox.confirm({
            title: "QUITAR PRODUCTO",
            message: pregunta,
            buttons: 
            {
                cancel: 
                {
                    label: 'Cancelar'
                },
                confirm: 
                {
                    label: 'QUITAR'
                }
            },
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);
                if(result)
                {
                    $(tr).remove();    
                    actualizaResumen();
                }
            }
        });
    
    });
    
    function buscar_producto()
    {
        $("#table_product_result").hide();
        $("#producto_busca").val("");
        $("#addProductoModal").modal('show');
    }

});

