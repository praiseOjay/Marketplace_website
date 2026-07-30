<?php

namespace App\Tests\Service;

use App\Service\ImageOptimizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageOptimizerTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/img_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $f) {
                @unlink($f);
            }
            @rmdir($this->tempDir);
        }
    }

    public function testProcessAdvertImageCreatesWebpAndThumbnail(): void
    {
        // Create dummy PNG image resource
        $samplePath = $this->tempDir . '/sample.png';
        $img = imagecreatetruecolor(800, 600);
        $red = imagecolorallocate($img, 255, 0, 0);
        imagefill($img, 0, 0, $red);
        imagepng($img, $samplePath);
        imagedestroy($img);

        $uploadedFile = new UploadedFile($samplePath, 'sample.png', 'image/png', null, true);
        $optimizer = new ImageOptimizer();

        $generatedFilename = $optimizer->processAdvertImage($uploadedFile, $this->tempDir);

        $this->assertStringEndsWith('.webp', $generatedFilename);
        $this->assertFileExists($this->tempDir . '/' . $generatedFilename);
        $this->assertFileExists($this->tempDir . '/thumb_' . $generatedFilename);

        // Assert thumbnail size is smaller or equal to 400x300
        $thumbInfo = getimagesize($this->tempDir . '/thumb_' . $generatedFilename);
        $this->assertLessThanOrEqual(400, $thumbInfo[0]);
        $this->assertLessThanOrEqual(300, $thumbInfo[1]);
    }
}
