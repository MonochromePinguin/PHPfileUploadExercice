<?php

/**
* Returns null if all gone well,
* or a error string if something went wrong
* @return string|null
*/
function init()
{
    if (! ini_set('upload_tmp_dir', DESTDIR)) {
        return 'erreur d\'initialisation PHP – code I1';
    }

    if (! init_set('upload_max_filesize', MAXFILESIZE)) {
        return 'erreur d\'initialisation PHP – code I2';
    }

    return null;
}
