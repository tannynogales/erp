/* global userID, bootbox */

$(document).ready(function()
{
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

    $('#precio, #costo, #precio_mayorista, #precio_web').priceFormat
    ({
        prefix: '$ ',
        centsSeparator: '.',
        thousandsSeparator: ',',
        centsLimit: 0,
        allowNegative: false
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
    var lista_codigos = [
      { label: "anders", category: "" },
      { label: "andreas", category: "" },
      { label: "antal", category: "" },
      { label: "annhhx10", category: "Products" },
      { label: "annk K12", category: "Products" },
      { label: "annttop C13", category: "Products" },
      { label: "anders andersson", category: "People" },
      { label: "andreas andersson", category: "People" },
      { label: "andreas johnson", category: "People" }
    ];
*/ 
    
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
                //alert(data);
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
    
    $("#codigo").on("keyup change", function(e) 
    //$( "#codigo" ).keyup(function()
    {
        var valor = $(this).val();
        valor = valor.toUpperCase();
        //alert(value);
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
    });    

});

