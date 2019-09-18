<?php

namespace DynamicImage;

use DynamicImage\Validate;
use DynamicImage\Cache;
use DynamicImage\Generate;
use DynamicImage\Exceptions\MissingParamException;
use DynamicImage\Exceptions\MissingImageException;

class DynamicImage
{
    // Array of images to be generated
    public $images = [];

    private $cacheDir;
    private $width;
    private $height;
    private $missingPlaceholderImage = '/images/missing.jpg';

    public function __construct($params)
    {
        Validate::clear();

        if (!Validate::input($params)) {
            throw new MissingParamException($this->errors()[0]);
        }

        $this->cacheDir = $params['cacheDir'] ?? "/../cache/";
        $this->width = $params['width'];
        $this->height = $params['height'];
    }

    public function generate($images)
    {
        $cache = new Cache($this->cacheDir);

        $images = $this->reformatImagesInput($images);

        $return = [];
        $errors = [];
        foreach ($images as $originalImage) {
            if ($this->missingPlaceholderImage !== false && !Validate::image($originalImage)) {
                $image = $this->missingPlaceholderImage;
            } else {
                $image = $originalImage;

                if ($this->missingPlaceholderImage === false) {
                    throw new MissingImageException("The image {$originalImage} does not exist");
                }
            }

            if (!$cache->exists($image)) {
                $generator = new Generate($image, $this->width, $this->height);
            }

            $return[$originalImage] = $cache->get($image);
        }

        return [
            'images' => $return,
            'errors' => Validate::errors()
        ];
    }

    public function errors()
    {
        return Validate::errors();
    }

    public function reformatImagesInput($images)
    {
        if (!is_array($images)) {
            $images = [$images];
        }

        return $images;
    }

    /**
     * This either takes an image path as input or `false` if the default behaviour should
     *  be to throw an exception if the generated image does not exist
     */
    public function setMissingPlaceholderImage($image)
    {
        if ((!is_bool($image) || $image !== false) && !Validate::image($image)) {
            throw new MissingImageException("The image {$image} does not exist");
        }

        $this->missingPlaceholderImage = $image;
    }
}
