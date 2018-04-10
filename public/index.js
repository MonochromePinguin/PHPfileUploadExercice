//I love IIFE
( function()
 {
    var p = document.getElementById('fileComments');
    var uploadButton = document.getElementById('uploadButton');
    var fileInput = document.getElementById('theFileInput');

    fileInput.addEventListener( 'change',
        function()
        {
            var error = false;

            for( f of this.files )
            {
                if( f.size > 1048576 )
                {
                   p.insertAdjacentHTML('beforeend',
                         'le fichier «' + f.name + '» pèse trop lourd&nbsp!<br>' )
                   error = true;
                }
            }

            if (error)
            {
                this.value = "";
                uploadButton.disabled = true;
            }
            else
                uploadButton.disabled = false;
        }
    );

    document.getElementById('resetButton').addEventListener( 'click',
        function()
        {
            fileInput.value = "";
            p.innerHTML = '';
            uploadButton.disabled = false;
        }
    );

 }
)();