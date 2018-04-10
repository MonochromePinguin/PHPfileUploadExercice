<?php


function generateSVGreload() : string
{
    return <<<EOS
    <svg height="32" width="32" class="octicon octicon-sync" viewBox="0 0 12 16" version="1.1" width="48" aria-hidden="true"><path fill-rule="evenodd" d="M10.236 7.4a4.15 4.15 0 0 1-1.2 3.6 4.346 4.346 0 0 1-5.41.54l1.17-1.14-4.3-.6.6 4.2 1.31-1.26c2.36 1.74 5.7 1.57 7.84-.54a5.876 5.876 0 0 0 1.74-4.46l-1.75-.34zM2.956 5a4.346 4.346 0 0 1 5.41-.54L7.196 5.6l4.3.6-.6-4.2-1.31 1.26c-2.36-1.74-5.7-1.57-7.85.54-1.24 1.23-1.8 2.85-1.73 4.46l1.75.35A4.17 4.17 0 0 1 2.956 5z"></path>
    </svg>
EOS;
}


function generateSVGcompact() : string
{
    return <<<EOS
    <svg width="32" height="32" class="octicon octicon-list-unordered" viewBox="0 0 12 16" version="1.1" aria-hidden="true"><path fill-rule="evenodd" d="M2 13c0 .59 0 1-.59 1H.59C0 14 0 13.59 0 13c0-.59 0-1 .59-1h.81c.59 0 .59.41.59 1H2zm2.59-9h6.81c.59 0 .59-.41.59-1 0-.59 0-1-.59-1H4.59C4 2 4 2.41 4 3c0 .59 0 1 .59 1zM1.41 7H.59C0 7 0 7.41 0 8c0 .59 0 1 .59 1h.81c.59 0 .59-.41.59-1 0-.59 0-1-.59-1h.01zm0-5H.59C0 2 0 2.41 0 3c0 .59 0 1 .59 1h.81c.59 0 .59-.41.59-1 0-.59 0-1-.59-1h.01zm10 5H4.59C4 7 4 7.41 4 8c0 .59 0 1 .59 1h6.81c.59 0 .59-.41.59-1 0-.59 0-1-.59-1h.01zm0 5H4.59C4 12 4 12.41 4 13c0 .59 0 1 .59 1h6.81c.59 0 .59-.41.59-1 0-.59 0-1-.59-1h.01z"></path>
    </svg>
EOS;
}


function generateSVGsend() : string
{
    return <<<EOS
     <svg height="32" class="octicon octicon-cloud-upload" viewBox="0 0 16 16" version="1.1" width="32" aria-hidden="true">
        <path fill-rule="evenodd" d="M7 9H5l3-3 3 3H9v5H7V9zm5-4c0-.44-.91-3-4.5-3C5.08 2 3 3.92 3 6 1.02 6 0 7.52 0 9c0 1.53 1 3 3 3h3v-1.3H3c-1.62 0-1.7-1.42-1.7-1.7 0-.17.05-1.7 1.7-1.7h1.3V6c0-1.39 1.56-2.7 3.2-2.7 2.55 0 3.13 1.55 3.2 1.8v1.2H12c.81 0 2.7.22 2.7 2.2 0 2.09-2.25 2.2-2.7 2.2h-2V12h2c2.08 0 4-1.16 4-3.5C16 6.06 14.08 5 12 5z"></path>
    </svg>
EOS;
}


function formatUploadMsg(string $msg) : string
{
    return "<aside class=\"alert alert-success\" role=\"alert\">\n<p>"
            . htmlspecialchars($msg)
            . "</p>\n</aside>\n" ;
}


function formatErrorMsg($msg = 'Erreur non spécifiée.') : string
{
    return "<aside class='error'>\n<h1>Désolé&nbsp!</h1>\n<p>
        Une erreur est survenue.<br>
        Veuillez contacter l'opérateur du site.<br>"
        . ( null != $mssg ?
                "Message d'erreur&nbsp:<br>\n<span class='norm'>"
                . htmlspecialchars($msg) . '</span>'
              :
                ''
        )
        . "\n</p>\n</aside>\n";
}




function generateImagesShower(array $files) : string
{

    if (0 == count($files)) {
        return <<<EOS
        <div class="alert alert-dark" role="alert">
            <p>
                Aucune image disponible
            </p>
        </div>
EOS;
    }

    $ret = '';

    #file counter
    $count = 0;

    foreach ($files as $f) {
        ++$count;

        $dateStr = strftime('%A %d/%B/%Y à %Hh%Mm%Ss ', $f['timestamp']);

        $origName = $f['name'] ?? 'nom d\'origine&nbsp;: inconnu';

        $serverName = $f['diskName'];

        $postedBy = isset($f['postedBy']) ?
                '<p>Posté par&nbsp;: <b>' . $f['postedBy'] . '</b></p>'  :  '';

        $description = isset($f['description']) ?
                '<p>description&nbsp;: <b>' . $f['description'] . '</b></p>'  :  '';

        $size = $f['size'];
        if ($size >= 1024) {
            $size = round($size/1024, 2) . 'Ko';
        } else {
            $size .= 'o';
        }

        $ret .= <<< EOS

        <article class="card m-3 p-1">
            <button class="card-img-top m-3 p-1 noButton"
                    data-toggle="modal" data-target="#imageModal{$count}"
                    title="Cliquez ici pour zoomer">
                <img class="img-responsive align-self-center"
                    src="{$f['url']}"  alt="Image absente&nbsp!" >
            </button>

            <div class="card-body">
              <h4 class="card-title">{$origName}</h4>

              <div class="card-text">
                <p>Sur le serveur&nbsp;: <b>{$serverName}</b></p>
                {$description}
                <p>Date de réception&nbsp;: <b>{$dateStr}</b></p>
                <p>Taille&nbsp;: <b>{$size}</b></p>
                {$postedBy}

                <button class="btn btn-danger m-1"
                        data-toggle="modal" data-target="#confirmDelete{$count}"
                        title="Attention, une seule dernière chance&nbsp;!">
                    supprimer l'image ...
                </button>
              </div> <!-- .card-text -->

            </div> <!-- .card-body -->

            <div class="modal fade" id="imageModal{$count}" tabindex="-1"
                    role="dialog" aria-hidden="true">

                <div class="modal-dialog" data-dismiss="modal">
                  <div class="modal-content">

                      <div class="modal-header">
                          <h5 class="modal-title">{$origName}</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                          </button>
                      </div>

                      <div class="modal-body">
                        <img src="{$f['url']}" style="width: 100%;" >
                      </div> <!--.modal-body -->

                    </div> <!-- .modal-content -->

                </div> <!-- .modal-dialog -->
            </div>

            <div class="modal fade" id="confirmDelete{$count}" tabindex="-1" role="dialog" aria-hidden="true">

                <div class="modal-dialog">
                  <div class="modal-content">

                      <div class="modal-header">
                          <h5 class="modal-title">
                            Sûr de vouloir supprimer cette image&nbsp;?
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                          </button>
                      </div>

                      <div class="modal-footer container-fluid">
                        <div class="row">
                            <form action="" method="POST" class="col">

                                <input type="hidden" name="fileToDelete"
                                    value="{$serverName}" >
                                <input type="submit"
                                    class="btn btn-danger"
                                    value="OUI" >
                            </form>

                            <button type="button"
                                class="m-3 col btn btn-secondary"
                                data-dismiss="modal" aria-label="Close">
                                    NON (c'est plus prudent)
                            </button>

                        </div> <!-- .row -->
                      </div> <!--.modal-footer -->

                    </div> <!-- .modal-content -->

                </div> <!-- .modal-dialog -->
            </div>

        </article>
EOS;
    }

    return $ret;
}
