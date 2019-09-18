<?php

namespace DynamicImage;

class Generate
{
    public function __construct($image, $width, $height, $cacheDir = "cache/")
    {
        $type = $this->getExtension($image);

        switch ($type) {
            case IMAGETYPE_JPEG:
                return $this->jpg($image, __DIR__ . "/..", $width, $height, $cacheDir);
                break;
            case IMAGETYPE_PNG:
                return png($image);
                break;
            case IMAGETYPE_GIF:
                return gif($image);
                break;
            default:
                return false;
        }
    }

    private function jpg($image, $path, $width, $height, $saveDir)
    {
        $fullImage = $path . $image;
        $imageResource = imagecreatefromjpeg($fullImage);

        $newImage = imagecreatetruecolor($width, $height);

        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $width, $height, imagesx($imageResource), imagesy($imageResource));

        imagejpeg($newImage, $saveDir . md5($image) . ".jpg");
    }

    private function png($image, $path, $width, $height, $saveDir)
    {
        $fullImage = $path . $image;
        $imageResource = imagecreatefrompng($fullImage);

        $newImage = imagecreatetruecolor($width, $height);

        imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0));
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $width, $height, imagesx($imageResource), imagesy($imageResource));

        imagepng($newImage, $saveDir . md5($image));
    }

    private function gif($image)
    {

    }

	private function getExtension($image)
	{
        return exif_imagetype(__DIR__ . "/../" . $image);
	}
}



/**
switch($this->extension) {
            case '.png':
                $image = imagecreatefrompng(
                    $this->imageDirectory . $this->filename .
                    $this->extension
                );
                break;
            case '.jpg':
            case '.jpeg':
                $image = imagecreatefromjpeg(
                    $this->imageDirectory . $this->filename .
                    $this->extension
                );
                break;
            case '.gif':
                $image = $this->resizeGif();

                $image = $image->deconstructImages();
                $image->writeImages('cache/' . $this->cachedFilename, true);

                $this->file = 'cache/' . $this->cachedFilename;

                // GIFs are handled differently so return here
                return;
            default:
                $this->error(DynamicImage::$_ERRORS['UNSUPPORTED_EXTENSION']['message'], DynamicImage::$_ERRORS['UNSUPPORTED_EXTENSION']['code']);
                return;
        }

        // Create new image resource
        $newImage = imagecreatetruecolor($this->width, $this->height);

        // If the image is `.png` make sure we respect it's alpha
        if ($this->extension == ".png") {
            imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Copy old image over new
        imagecopyresampled(
            $newImage, $image, 0, 0, 0, 0, $this->width,
            $this->height, imagesx($image), imagesy($image)
        );
        
        // Save the new image as the correct filetype
        switch($this->extension) {
            case '.png':
                imagepng($newImage, 'cache/' . $this->cachedFilename);
                break;
            case '.jpg':
            case '.jpeg':
                imagejpeg($newImage, 'cache/' . $this->cachedFilename);
                break;
            case '.gif':
                imagegif($newImage, 'cache/' . $this->cachedFilename);
                break;
        }

 */
