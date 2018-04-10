This repo contains basic structure for listing files present in a directory
or deleting them, with some basic metadatas management, and a form to upload
 new files (of certain specific file formats).

● how to use it:
As usually, go into the "public" subdir and run the PHP server from there.

• structure:
    public:             web-accessible files
    public/files:       uploaded files
    src:                ...
    references:         store the available metadatas of the images
                         as JSON text files ending with ".ref"
                        (we CANNOT use a DB in this exercice, right ?)

