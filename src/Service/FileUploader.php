<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
        private ImageOptimizer $imageOptimizer,
    ) {
    }

    public function upload(UploadedFile $file, bool $isProfile = false): string
    {
        if ($isProfile) {
            return $this->imageOptimizer->processProfileImage($file, $this->getTargetDirectory());
        }

        return $this->imageOptimizer->processAdvertImage($file, $this->getTargetDirectory());
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}