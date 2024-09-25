<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        // check if $file is an instance of UploadedFile
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // get original filename from client's browser
        $safeFilename = $this->slugger->slug($originalFilename);
        // generate a unique name for the file
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // move the file to the directory where brochures are stored
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        // return the filename
        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        //returns the current target directory
        return $this->targetDirectory;
    }
}