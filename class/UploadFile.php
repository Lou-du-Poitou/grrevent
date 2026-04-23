<?php

class UploadFile
/**
 * Permet de gérer le téléchargement des fichiers
 */
{
    private string | null $fileName = null;
    private int | null $fileStatus = null;
    private string | null $fileTmp = null;
    private string | null $mimeType = null;

    private function getMimeType(): string | false
    /**
     * Renvoie le mime type du fichier
     * 
     * @return string
     */
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        if (!$info || !$this->fileTmp) {
            return false;
        }

        $mimeType = finfo_file($info, $this->fileTmp);
        finfo_close($info);

        return $mimeType;
    }

    public function __construct(array $file)
    /**
     * @var string $file
     */
    {
        if (
            !isset($file['name']) || 
            !isset($file['error']) || 
            !isset($file['tmp_name'])
        ) {
            throw new Exception('fichier invalide ou inexistant');
        }

        $this->fileName = $file['name'];
        $this->fileStatus = $file['error'];
        $this->fileTmp = $file['tmp_name'];
        $this->mimeType = $this->getMimeType();
    }

    public function checkSize()
    /**
     * Vérifie que la taille du fichier est pas trop grande
     * 
     * @return bool
     */
    {
        return filesize($this->fileTmp) < MAX_FILE_UPLOAD;
    }

    public function checkImgFormat()
    /**
     * Renvoie le type de l'image ou false si ce n'en est pas une
     * 
     * @return string
     * @return false
     */
    {
        if (!in_array(
            $this->mimeType, 
            array_keys(IMAGE_FORMATS)
        )) {
            return false;
        }

        return $this->mimeType;
    }

    public function getStatus()
    /**
     * Permet de renvoyer le status de l'upload
     * 
     * @return int
     */
    {
        return $this->fileStatus;
    }

    public function moveFile(string $path): void
    /**
     * Permet de déplacer un fichier téléchargé
     * 
     * @var string $path
     * 
     * @return void
     */
    {        
        move_uploaded_file(
            $this->fileTmp, 
            $path
        );
    }
}
