<?php

define('TMPDIR', '/tmp/imgUpload/');
# *** MUST end with a slash *** :
    # where to put images files
define('DESTDIR', 'files/');
    # where to put theire (private) description
define('REFDIR', '../references/');

define('MAXFILESIZE', 1048576);
define('ALLOWEDMIMETYPES', [ 'image/jpeg', 'image/png', 'image/gif' ]);

define('INPUTNAME', 'files');
