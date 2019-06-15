<?php

namespace DynamicImage;

class Cache
{
    private $cacheDir;

    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;

        if (!$this->dirExists(__DIR__ . $cacheDir)) {
            $this->createDir(__DIR__ . $cacheDir);
        }
    }

    public function exists($image)
    {
        return $this->fileExists(__DIR__ . $this->cacheDir . md5($image));
    }

    public function get($image)
    {
        if (!$this->exists($image)) {
            return false;
        }

        return __DIR__ . $this->cacheDir . md5($image);
    }

    private function fileExists($fullPath)
    {
        return file_exists($fullPath);
    }

    private function dirExists($dirPath)
    {
        return is_dir($dirPath);
    }

    private function createDir($dirPath)
    {
        return mkdir($dirPath, 0755, true);
    }
}
