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
        $extension = explode(".", $image);
        $extension = $extension[sizeof($extension) - 1];

        return $this->fileExists(__DIR__ . $this->cacheDir . md5($image) . ".{$extension}");
    }

    public function get($image)
    {
        $extension = explode(".", $image);
        $extension = $extension[sizeof($extension) - 1];

        if (!$this->exists($image)) {
            return false;
        }

        return $this->cacheDir . md5($image) . ".{$extension}";
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
