<?php

    require_once '../src/constants.php';
    require_once '../src/init.php';
    require_once '../src/modelFunctions.php';
    require_once '../src/vueFunctions.php';

    #needed by strftime to output localized time to **system's current locale**
    setlocale(LC_ALL, null);
    init();

    $errorFlag = false;
    $operatIonMsg = null;

if (0 != count($_GET)) {
    $errorFlag = true;
    $errorMsg = formatErrorMsg('Cette page n\'est pas prévue pour être utilisée avec cette méthode HTTP');
} elseif (0 != count($_POST)) {
    ## file upload ?
    #
    if (isset($_FILES[INPUTNAME])
         &&  0 != ( $nb = count($_FILES[INPUTNAME]['name']) )
       ) {
        $uploadErrors = 0;

        # simple shortcuts
        $names = $_FILES[INPUTNAME]['name'];
        $sizes = $_FILES[INPUTNAME]['size'];
        $types =  $_FILES[INPUTNAME]['type'];
        $tmp_names =  $_FILES[INPUTNAME]['tmp_name'];
        $errors =  $_FILES[INPUTNAME]['error'];

        #these fields are, for now, shared by all files of an upload
        $description = $_POST['description'] ?? null;
        $postedBy = $_POST['uploader'] ?? null;

        for ($i = 0; $i < $nb; ++$i) {
            $name = $names[$i];
            switch (treatFileUpload([
                        'name' => $name,
                        'size' => $sizes[$i],
                        'type' => $types[$i],
                        'tmp_name' => $tmp_names[$i],
                        'error' => $errors[$i],
                        'description' => $description,
                        'postedBy' => $postedBy,
                      ])
                   ) {
                case 0:
                    break;

                case FU_TOOBIG:
                    ++$uploadErrors;
                    $operationMsg .= 'le fichier «'
                    . protect($name) . '» pèse plus d\'1 Mo.<br>';
                    break;
                case FU_FILETYPE:
                    ++$uploadErrors;
                    $operationMsg .= 'le type du fichier «'
                    . protect($name) . '» n\'est pas accepté ('
                    . protect($type[$i]) . ').<br>';
                    break;
                case FU_NETWORK:
                    ++$uploadErrors;
                    $operationMsg .= 'une probable erreur réseau a empéché le transfert de «' . protect($name) . '».<br>';
                    break;
                case FU_SERVER:
                    ++$uploadErrors;
                    $operationMsg .= 'Erreur interne au serveur. Impossible de transférer «' . protect($name) . '».<br>';
            }
        }

        if (0 == $uploadErrors) {
            $operationMsg = formatUploadMsg('Téléversement réussi');
        } else {
            $operationMsg =
                 "<aside class=\"alert alert-error\" role=\"alert\">\n<p>"
                . $operationMsg . "</p>\n</aside>\n";
        }

        unset($_POST);
        unset($_FILES);

        ## file deletion ?
        #
    } elseif (isset($_POST['fileToDelete'])) {
        $name = $_POST['fileToDelete'];
        $res = deleteFile($name);

        if (null === $res) {
            $operationMsg = "<aside class=\"alert alert-success\">\n<p>Suppression de «" . $name . "» réussie</p>\n</aside>\n";
        } else {
            $operationMsg = "<aside class=\"alert alert-error\">\n<p>"
                . $res . "</p>\n</aside>\n";
        }
    }
}

    # always done – List present items
    $files = loadImageList(DESTDIR);

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>formulaire d'envoi de fichiers</title>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- pour désactiver le mode
     «compatibilité avec le vieil et défaillant IE » de edge -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- bootstrap 4 links -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">


    <!-- Latest compiled and minified JavaScript -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous" defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous" defer ></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous" defer></script>

    <link rel="stylesheet" href="default.css?v=2">
  </head>

  <body>

<?php if ($errorFlag) {
    echo $errorMsg;
} else {
?>

    <header>
        <h1>Images en stock</h1>
    </header>
    <hr class="hr1">
    <hr class="hr2">

    <aside class="container-fluid">
        <div class="row justify-content-around">

            <a href="/" class="btn btn-primary m-3"
               title="Recharger la page pour tenir compte d'éventuels changements récents">
                <?= generateSVGreload() ?>
                <span class="align-top">Recharger la page</span>
            </a>

<!--TODO – MODIFY by php or JS -->
<!--
    I'm so sorry I did'nt had the time ...
             <a href="#?show=compact"
              title="Utiliser un affichage en colonne des entrées"
              class="btn btn-primary m-3">
                <?= generateSVGcompact() ?>
                <span class="align-top">Affichage compact</span>
            </a>
 -->

            <button type="button" class="btn btn-primary m-3"
                title="Envoyer un ou des fichiers via un formulaire"
                data-toggle="modal"
                data-target="#uploadForm">
                <?= generateSVGsend() ?>
                <span class="align-top">Téléverser des fichiers</span>
            </button>

            <!-- Modal related to our marvelous button -->
            <div class="modal fade" id="uploadForm" tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

              <div class="modal-dialog modal-dialog-centered"
                   role="document">

                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title">
                              Téléversement de fichiers
                          </h5>
                          <hr class="hr1">
                          <hr class="hr2">

                          <button type="button" class="close"
                              data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div> <!-- .modal-header -->

 <!-- here start the form -->
                      <form action="" method="POST" id="fileSending"
                            enctype="multipart/form-data">

                        <fielset class="modal-body">
                            <!-- Tell the browser the max filesize ... -->
                            <input type="hidden" name="MAX_FILE_SIZE"
                                value="<?= MAXFILESIZE ?>">

                <!-- the [] says «it is an array !»,
                the "multiple" property is needed,
                "accept" won't exempt you from using server-side validation-->
                            <input type="file" name="<?= INPUTNAME ?>[]"
                                id="theFileInput"
                                accept="image/png, image/jpeg, image/gif"
                                multiple="multiple">

                            <!-- this p is filled by js if file is too big -->
                            <p id="fileComments" class="error"></p>

                            <label>
                                Descrition du téléversement&nbsp;:
                                <input type="text" name="description"
                                        placeholder="description ...">
                            </label>
                            <label>
                                Utilisateur&nbsp;:
                                <input type="text" name="uploader"
                                    placeholder="on ne devrait pas avoir à demander çà ..."">
                            </label>
                        </fieldset> <!-- .modal-body -->

                        <fieldset class="modal-footer">
<!-- todo : make this button work -->
                          <input type="reset"
                            id="resetButton"
                            class="btn btn-secondary" data-dismiss="modal"
                            value="Annuler" >

                          <input type="submit" name="sendFiles"
                             id="uploadButton" disabled
                             value="Envoyer" class="btn btn-primary" >
                        </fieldset> <!-- .modal-footer -->

                      </form>
<!-- it is said this is here the form ended -->

                  </div> <!-- .modal-content -->

              </div> <!-- .modal-dialog -->

          </div>  <!-- .modal -->

        </div> <!-- .row -->
    </aside>
    <hr class="hr1">
    <hr class="hr2">

<?= $operationMsg ?? '' ?>

    <main>

<?= generateImagesShower($files) ?>

    </main>

<?php
}
?>

  </body>

  <script src="index.js" defer></script>

</html>
