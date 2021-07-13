<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function __construct(private string $targetDir)
    {
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid('PIC', true)) . '.' . $file->guessExtension();

        $file->move($this->targetDir, $fileName);

        return $fileName;
    }


}