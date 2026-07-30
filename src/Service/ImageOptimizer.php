<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageOptimizer
{
    /**
     * Process an advert image upload:
     * 1. Converts to WebP (quality 82).
     * 2. Resizes main image to max 1200x800.
     * 3. Creates thumbnail resized to max 400x300.
     *
     * @return string The generated webp filename (stored in targetDir)
     */
    public function processAdvertImage(UploadedFile $file, string $targetDir): string
    {
        $filename = uniqid('ad_', true) . '.webp';
        $fullPath = $targetDir . '/' . $filename;
        $thumbPath = $targetDir . '/thumb_' . $filename;

        $sourceImage = $this->createSourceImage($file->getPathname());
        if (!$sourceImage) {
            // Fallback: move original file if GD processing fails
            $fallbackName = uniqid('ad_', true) . '.' . $file->guessExtension();
            $file->move($targetDir, $fallbackName);
            return $fallbackName;
        }

        // 1. Save Full Image (max 1200x800)
        $fullResized = $this->resizeImage($sourceImage, 1200, 800);
        imagewebp($fullResized, $fullPath, 82);
        if ($fullResized !== $sourceImage) {
            imagedestroy($fullResized);
        }

        // 2. Save Thumbnail (max 400x300)
        $thumbResized = $this->resizeImage($sourceImage, 400, 300);
        imagewebp($thumbResized, $thumbPath, 80);
        if ($thumbResized !== $sourceImage) {
            imagedestroy($thumbResized);
        }

        imagedestroy($sourceImage);

        return $filename;
    }

    /**
     * Process a user profile avatar upload:
     * Resizes to 200x200 square WebP.
     */
    public function processProfileImage(UploadedFile $file, string $targetDir): string
    {
        $filename = uniqid('avatar_', true) . '.webp';
        $targetPath = $targetDir . '/' . $filename;

        $sourceImage = $this->createSourceImage($file->getPathname());
        if (!$sourceImage) {
            $fallbackName = uniqid('avatar_', true) . '.' . $file->guessExtension();
            $file->move($targetDir, $fallbackName);
            return $fallbackName;
        }

        $resized = $this->resizeImage($sourceImage, 200, 200);
        imagewebp($resized, $targetPath, 82);

        if ($resized !== $sourceImage) {
            imagedestroy($resized);
        }
        imagedestroy($sourceImage);

        return $filename;
    }

    /**
     * Load GD image resource from file path.
     */
    private function createSourceImage(string $path)
    {
        $info = @getimagesize($path);
        if (!$info) {
            return false;
        }

        $mime = $info['mime'];
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };
    }

    /**
     * Proportional resize image resource to fit inside maxWidth x maxHeight.
     */
    private function resizeImage($source, int $maxWidth, int $maxHeight)
    {
        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth <= $maxWidth && $origHeight <= $maxHeight) {
            return $source;
        }

        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = (int) round($origWidth * $ratio);
        $newHeight = (int) round($origHeight * $ratio);

        $target = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        return $target;
    }
}
