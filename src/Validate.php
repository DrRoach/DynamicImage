<?php

namespace DynamicImage;

class Validate
{
    private static $errors = [];

    public static function input($params)
    {
        if (empty($params)) {
            self::$errors[] = "Missing parameters";

            return false;
        }

        if (empty($params['width'])) {
            self::$errors[] = "Missing width";
        }

        if (empty($params['height'])) {
            self::$errors[] = "Missing height";
        }

        if (!empty(self::$errors)) {
            return false;
        }

        return true;
    }

    public static function image($image)
    {
        if (!file_exists(__DIR__ . "/.." . $image)) {
            $errors[] = "Image {$image} does not exist.";
            return false;
        }

        return true;
    }

    public static function errors()
    {
        return self::$errors;
    }
}
