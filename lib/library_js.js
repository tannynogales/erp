$(document).ready(function(){
    //alert("hola!");  
});

$.fn.rutFormat = function()
{
	return this.each(function()
	{
		$(this).Rut({format_on: 'keyup'});
 		
		$(this).blur(function() 
                {
                    var rut = $(this).val();
                    rut = $.Rut.quitarFormato(rut);
                    isEmpty(rut);
                    if(isEmpty(rut))
                    {
                        //alert("vacio")
			$(this).addClass('ui-state-error');
                    }
                    
                    if($.Rut.validar(rut))
                    {
                        //alert("valido");
                        $(this).removeClass('ui-state-error');
                    }
                    else
                    {
                        //alert("no valido")
                        $(this).addClass('ui-state-error');
                    }
		});	
			
		$(this).keypress(function() {	
			sinEspacios(this);			
		});
		
		$(this).keyup(function() {
			sinEspacios(this);
			var rut = $(this).val();
			rut = $.Rut.quitarFormato(rut);
			//alert(rut);
		if(isEmpty(rut)){
			//alert("vacio")
			$(this).addClass('ui-state-error');
			}
		if($.Rut.validar(rut)){
			//alert("valido");
			$(this).removeClass('ui-state-error');
		}else{
			//alert("no valido")
			$(this).addClass('ui-state-error');
			}			
		});
	});
};

function sinEspacios(obj){
	var valor = $(obj).val();
		valor += "";
	var corregido = "";
	largo = valor.length;
	for (i=0;i<=largo-1;i++){
		if(valor[i]!=" "){
			corregido += valor[i];
			}
		}
	$(obj).attr('value', corregido);	
}

function isEmpty(valor){
	var isEmpty = true;
	var largo = valor.length;
	//alert(largo);
	for (i=0;i<=largo-1;i++){
		//alert(valor[i]);      
		if(valor[i]!=" "){
			isEmpty = false;
			}
		}	
	return isEmpty;
}