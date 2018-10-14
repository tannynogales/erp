$(document).ready(function()
{
   //$('#rut, #rut_busca').rutFormat();
    $( "#fecha_movimiento, #fecha_registro" ).datepicker
    ({
        dateFormat: "dd/mm/yy",
        minDate: "-19D", 
        maxDate: "+1M +1D",
        changeMonth: false,
        changeYear: false        
    });
    
    $("#id_busca").focus();
    
    $('#productos_form input[campo="cantidad"]').priceFormat
    ({
        prefix: '',
        centsSeparator: '.',
        thousandsSeparator: ',',
        limit: 3,
        centsLimit: 0,
        allowNegative: false
    });
                        
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
                    //actualizaResumen();
                }
            }
        });
    
    });    

    $( "#button_addProductoModal" ).click(function() 
    {
        buscar_producto();
    });
    
    function buscar_producto()
    {
        $("#table_product_result").hide();
        $("#producto_busca").val("");
        $("#addProductoModal").modal('show');
    }
    
    $('#addProductoModal').on('shown.bs.modal', function () 
    {
        $('#producto_busca').focus();
    });
    
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
                    if( existe === 0 )
                    {
                        var tr = "";
                        tr += '<tr id="productoFila_'+prod_id+'">';
                        tr += ' <td><input campo="id" name="producto_id_'+prod_id+'" type="text" class="form-control" value="'+prod_id+'" readonly></td>';
                        tr += ' <td campo="codigo">'+cod+'</td>';
                        tr += " <td>"+nombre+"</td>";
                        tr += ' <td><input campo="cantidad" name="producto_cantidad_'+prod_id+'" type="text"  class="form-control" value="1"></td>';
                        //tr += ' <td><input campo="precio"   name="producto_precio_'+prod_id+'"   type="text"  class="form-control" value="'+precio+'"></td>';
                        //tr += ' <td campo="total"></td>';
                        tr += ' <td><span class="glyphicon glyphicon-remove" aria-hidden="true" producto_id="'+prod_id+'"></span></td>';
                       
                        tr += "</tr>";
                        $("#productos_form tbody").append(tr);
                        //http://jquerypriceformat.com/#examples
                        
                        //actualizaFila( $("#productoFila_"+prod_id) );
                        /*
                        $('#productos_form input[name="producto_precio_'+prod_id+'"]').priceFormat
                        ({
                            prefix: '$ ',
                            centsSeparator: '.',
                            thousandsSeparator: ',',
                            centsLimit: 0,
                            allowNegative: false
                        });
                        */
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
    
});