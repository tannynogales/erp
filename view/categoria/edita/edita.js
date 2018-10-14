$(document).ready(function()
{
    $('#input-rut').rut({
        on_error: function(){ alert('Rut incorrecto'); },
        on_success: function(){ alert('Rut correcto'); }
    });
});