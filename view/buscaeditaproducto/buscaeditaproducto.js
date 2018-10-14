$(document).ready(function()
{
    $("#id_busca").focus();
    
    $('#precio, #precio_mayorista, #precio_web, #costo').priceFormat
    ({
        prefix: '$ ',
        centsSeparator: '.',
        thousandsSeparator: ',',
        centsLimit: 0,
        allowNegative: false
    });
    
    tinymce.init({
        selector: 'textarea',
        height: 150,
        menubar: false,
        plugins: 
        [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        content_css: '//www.tinymce.com/css/codepen.min.css'
    });   
    
    $.widget( "custom.catcomplete", $.ui.autocomplete, {
         _create: function() {
           this._super();
           this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
         },
         _renderMenu: function( ul, items ) {
           var that = this,
             currentCategory = "";
           $.each( items, function( index, item ) {
             var li;
             if ( item.category != currentCategory ) {
               ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
               currentCategory = item.category;
             }
             li = that._renderItemData( ul, item );
             if ( item.category ) {
               li.attr( "aria-label", item.category + " : " + item.label );
             }
           });
         }
    });    
    /*
    var controller = "producto";
    var action     = "buscaProducto";
    var valor      = "";
    var codigos    = new Array();
    $.ajax
    ({
        url: '?controller='+controller+'&action='+action,
        type: 'POST',
        data:
            'valor='+valor,
            dataType: "json",
            async:true,
            success: function(data, textStatus)
            {
                $.each( data["html"], function( key, value ) {
                    codigos[key] = { label: (value.codigo).toUpperCase(), category: "" };
                });                
                $( "#codigo" ).catcomplete({
                  delay: 0,
                  source: codigos
                });
            },
            error: function (request, status, error) 
            {
                alert("si error: "+request.responseText);
            }            
    });
    */
    $("#codigo").on("keyup change", function(e) 
    //$( "#codigo" ).keyup(function()
    {
        var valor_old = $(this).attr("value_old");
        valor_old     = valor_old.toUpperCase();
        
        var valor     = $(this).val();
        valor = valor.toUpperCase();
        //alert(value);
        if (valor == valor_old)
        {
            $("#submit_button").removeAttr('disabled');
            $("#codigo_advertencia").hide();            
        }
        else
        {
            var do_exist = false;
            $.each( codigos, function( key, value ) 
            {
                //alert("hola");
                if( value["label"] === valor)
                {
                    //alert("mostrar");
                    //alert("encontrado: "+value["label"]+", "+valor);
                    $("#submit_button").attr('disabled','disabled');
                    $("#codigo_advertencia").show();
                    do_exist = true;
                    return false;
                }
            }); 
            if(do_exist === false)
            {
                //alert("no encontrado");
                $("#submit_button").removeAttr('disabled');
                $("#codigo_advertencia").hide();
            }            
        }
    });   
    
});
    
function number_format(value)
{
    var decimales = 0;
    //var value  = 724250.223;
    var numero = '$ ' + value.toFixed(decimales).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    return numero;
}

