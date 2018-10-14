$(document).ready(function()
{
   $('#rut').rutFormat();
   
    $( ".edit" ).click(function() {
        //$( this ).slideUp();+
        //$( "#rut" ).focus();
        var userID = $(this).attr("userID");
        var tr = $('tr[userID="'+userID+'"]');
        //var td = $(tr).find("td");
        // $(td[0]).html()
        $("#id")      .attr("value", userID);
        $("#rut")     .attr("value", $(tr).find("td[campo='rut']").html());
        $("#nombre")  .attr("value", $(tr).find("td[campo='nombre']").html());
        $("#apellido").attr("value", $(tr).find("td[campo='aPaterno']").html());
        $("#email")   .attr("value", $(tr).find("td[campo='email']").html());
        $("#password").attr("value", $(tr).find("td[campo='password']").html());
        
        $("#accion").attr("value", "Actualizar");
        $("#form_button").html("Actualizar");
        
        $("html, body").animate({ scrollTop: 0 }, 600);
    });

});