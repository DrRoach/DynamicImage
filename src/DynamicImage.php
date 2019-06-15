<?php

namespace DynamicImage;

use DynamicImage\Validate;
use DynamicImage\Cache;
use DynamicImage\Generate;

class DynamicImage
{
    private $cacheDir;
    private $width;
    private $height;
    
    public function __construct($params)
    {
        if (!Validate::input($params)) {
            return;
        }

        $this->cacheDir = $params['cacheDir'] ?? "/../cache/";
        $this->width = $params['width'];
        $this->height = $params['height'];
    }

    public function generate($images)
    {
        $cache = new Cache($this->cacheDir);

        $return = [];
        $errors = [];
        foreach ($images as $image) {
            if (!Validate::image($image)) {
                $errors[] = Validate::errors();
                continue;
            }

            if (!$cache->exists($image)) {
                $generator = new Generate($image, $this->width, $this->height);
            }

            $return[] = $cache->get($image);
        }

        return [
            'images' => $return,
            'errors' => $errors
        ];
    }

    public function errors()
    {
        return Validate::errors();
    }
}
