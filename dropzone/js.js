$(document).ready(function()
{

    // Disabling autoDiscover, otherwise Dropzone will try to attach twice.
    //Dropzone.autoDiscover = false;
    // or disable for specific dropzone:
    Dropzone.options.myId = false;

    $("#myId").dropzone({
        url: "upload.php",
        /*
         * @addRemoveLinks
         * If true, this will add a link to every file preview to remove or 
         * cancel (if already uploading) the file. The dictCancelUpload, 
         * dictCancelUploadConfirmation and dictRemoveFile options are used for
         *  the wording.
         */
	addRemoveLinks: true,
        /*
         * @previewsContainer
         * Defines where to display the file previews – if null the Dropzone 
         * element itself is used. 
         * Can be a plain HTMLElement or a CSS selector. 
         * The element should have the dropzone-previews class so the previews 
         * are displayed properly.
         */
        previewsContainer: '.dropzone-previews',
        dictCancelUpload: "asd",
        dictCancelUploadConfirmation: "bb",
        dictRemoveFile: "dd",
	maxFileSize: 1000,
        previewTemplate: document.getElementById("dropzone_template").innerHTML, 
        /*
         * @dictResponseError
         * null
         * If the server response was invalid. {{statusCode}} will be replaced
         *  with the servers status code.
         */
	dictResponseError: "Ha ocurrido un error en el server",
        acceptedFiles: 'image/*,.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF,.rar,application/pdf,.psd',
        init: function () 
        {
            this.on("sending", function(file, xhr, formData){
                formData.append("otro_dato", "loremipsum");
            });            
        },
        complete: function(file)
        {
            if(file.status === "success")
            {
                alert("El siguiente archivo se ha subido correctamente: " + file.name);
            }
	},
	error: function(file)
	{
            alert("Error subiendo el archivo " + file.name);
	},
	removedfile: function(file)
	{
            var name = file.name;
            alert(name);
            /* 
            $.ajax({
                type: "POST",
		url: "upload.php?delete=true",
		data: "filename="+name,
		success: function(data)
		{
                    var json = JSON.parse(data);
                    if(json.res == true)
                    {
                        var element;
			(element = file.previewElement) != null ? 
			element.parentNode.removeChild(file.previewElement) : 
			false;
			alert("El elemento fué eliminado: " + name); 
                    }
		}
            });
            */
	}
    });
     
    
});

