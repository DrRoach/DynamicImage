<?php

namespace DynamicImage;

class Validate
{
    private static $errors = [];

    public static function input($params)
    {
        if (empty($params)) {
            static::$errors[] = "Missing parameters";

            return false;
        }

        if (empty($params['width'])) {
            static::$errors[] = "Missing width";
        }

        if (empty($params['height'])) {
            static::$errors[] = "Missing height";
        }

        if (!empty(static::$errors)) {
            return false;
        }

        return true;
    }

    public static function image($image)
    {
        if (!file_exists(__DIR__ . "/.." . $image)) {
            static::$errors[] = "Image {$image} does not exist.";
            return false;
        }

        return true;
    }

    public static function errors()
    {
        return static::$errors;
    }

    public static function clear()
    {
        static::$errors = [];
    }
}
