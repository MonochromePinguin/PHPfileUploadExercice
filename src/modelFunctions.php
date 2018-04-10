<?php

require_once 'constants.php';

# FU for «File Upload »
define('FU_TOOBIG', 1);
define('FU_FILETYPE', 2);
define('FU_NETWORK', 4); #something make the file not arrive fully
define('FU_SERVER', 5); # internal server error (write access denied, etc.)

/**
* validate an uploaded file's metadatas sent as an associative array and,
* if all is ok, save the file AND an associated JSON file (ending with the
* extension ".ref") into REFDIR where its metadatas are stored. If the
* associated .ref cannot be stored, don't care.
*
* return an error code – 0 if all is ok
* @param array $file should contains these properties :
*               name, tmp_name, type, size, error, postedBy, description
* @return int
*/
function treatFileUpload(array $file) : int
{

    $origName = $file['name'];
    $name = $file['tmp_name'];

    # it was the last file : $file['tmp_name'] is empty
    if (empty($name)) {
        return 0;
    }

    if (( UPLOAD_ERR_INI_SIZE === $file['error'] )
        || ( UPLOAD_ERR_FORM_SIZE === $file['error'] )
        || ( $file['size'] > MAXFILESIZE )
        || ( filesize($name) > MAXFILESIZE )
    ) {
        return  FU_TOOBIG;
    }


    $mimetype =  mime_content_type($name);

    if (! in_array($file['type'], ALLOWEDMIMETYPES)
        && ! in_array($mimetype, ALLOWEDMIMETYPES)
    ) {
        return FU_FILETYPE;
    }

    switch ($file['error']) {
        case 0:
            break;
        case UPLOAD_ERR_PARTIAL:
        case UPLOAD_ERR_NO_FILE:
            return FU_NETWORK;
        default:
            return FU_SERVER;
    }

    #uniqid is badly named : it's just a microsecond timestamp ...
    do {
        $newName = 'image' . uniqid() . hash('md5', $origName)
                     . '.' . substr($mimetype, 6);
    } while (file_exists(DESTDIR . $newName));

    if (! move_uploaded_file($name, DESTDIR . $newName)) {
        return FU_SERVER;
    }

    # create the "reference file" which store informations about the file
    $data = json_encode([
             'name' => $origName,
             'postedBy' => $file['postedBy'] ?? 'Anonymous Uploader',
             'description' => $file['description'] ?? null,
                ]);
    $fd = fopen(REFDIR . $newName . '.ref', 'w');

    #we can live without this file ...
    if (false !== $data  && false !== $fd) {
        # ... but it's better to save it
        fwrite($fd, $data);
        fclose($fd);
    }

    # UNTOUCHED FILES WILL BE DELETED AT END OF SCRIPT !
    return 0;
}

/**
* @param string|null $str
* @return string|null
*/
function protect($str)
{
    return  isset($str) ?
        htmlspecialchars($str)
        : $str;
}



/**
* return an array of associative arrays storing metadatas about the files
*presents into DESTDIR ; some of these datas are read from the associated
* ".ref" file in REFDIR, if this file is present.
*/
function loadImageList(string $directory) : array
{

    $dir = new DirectoryIterator($directory);
    $ret = [];

    foreach ($dir as $file) {
        if ($file->isDot()) {
            continue;
        }

        #on-disk fileName – ≠ the original fileName
        $diskName = protect($file->getFilename());

        $f = [];
        $f['url'] = protect($directory) . $diskName;
        $f['size'] =  $file->getSize() ;
        $f['timestamp'] = $file->getMTime();
        $f['diskName'] = $diskName;

        #this ".ref" file stores all related datas
        $refFile = REFDIR . $diskName . '.ref';
        if (file_exists($refFile)
             && ( false !== ( $data = file_get_contents($refFile) ) )
             && ( false !== ( $refs = json_decode($data, true) ) )
        ) {
            $f['name'] = protect($refs['name']);
            $f['postedBy'] = protect($refs['postedBy']);
            $f['description'] = protect($refs['description']);
        }

        $ret[] = $f;
    }

    return $ret;
}


/**
* delete a file given its basename.
* @return string|null null if all was ok, or an error message
*           to tell what goes wrong.
*/
function deleteFile(string $file)
{
    $f = DESTDIR . $file;
    $ref = REFDIR . $file . '.ref';

    if (! file_exists($f)) {
        return 'fichier «' . $file . '» introuvable' ;
    }

    if (unlink($f)) {
        if (file_exists($ref)) {
            if (! unlink(REFDIR . $file . '.ref')) {
                return 'erreur de suppression du fichier de référence «'
                            . REFDIR . $file . '.ref»';
            }
        }

        return null;
    }

    return 'erreur de suppression du fichier «' . $file . '»' ;
}
