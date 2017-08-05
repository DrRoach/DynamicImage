<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * This script was made to quickly and safely resize images on the fly
 * and cached the generated image so that images added to a site
 * don't need to be resized when they are added or uploaded.
 *
 * PHP version 5.4
 *
 * @category Image
 * @package  DynamicImage
 * @author   Ryan Deas <ryandeas1@gmail.com>
 * @license  MIT 2
 * @link     http://google.com
 */

/**
 * Class DynamicImage
 *
 * Returns either the resized image filename or false
 *
 * PHP version 5.4
 *
 * @category Image
 * @package  DynamicImage
 * @author   Ryan Deas <ryandeas1@gmail.com>
 * @license  MIT 2
 * @link     http://google.com
 */
class DynamicImage
{
    // File that is used by the user
    public $file = null;

    // The name of the image that is to be generated
    private $filename = null;
    // Extention of the image that is to be generated
    private $extension = null;
    // The width and height of the image that is to be generated
    private $width = 0;
    private $height = 0;
    // The directory holding the images if they aren't in the same folder as this
    private $imageDirectory = null;
    // The cached version of the generated file
    private $cachedFilename = null;

    /**
     * First function that is called and also the function that calls
     * all other parts of this file.
     * 
     * User supplies array of parameters.
     */
    function __construct(Array $params = [])
    {
        /**
         * Error checking and handling
         */
        if (empty($params['filename']) || empty($params['width'])
            || empty($params['height'])
        ) {
            return;
        }

        /**
         * Store the filename, width, height and image directory and then check the
         * cache to see if this file already exists
         */
        $finalPeriod = strrpos($params['filename'], '.');
        //Don't allow '..' to prevent directory traversal
        $cleanFilename = str_replace('..', '', $params['filename']);
        $this->filename = substr($cleanFilename, 0, $finalPeriod);
        $this->extension = substr($cleanFilename, $finalPeriod);

        $this->width = $params['width'];
        $this->height = $params['height'];

        /**
         * This MUST end with a trailing slash
         */
        if (!empty($params['imageDirectory'])) {
            //Don't allow '..' to prevent directory traversal
            $this->imageDirectory = str_replace('..', '', $params['imageDirectory']);
        }

        $this->cachedFilename = $this->filename . $this->width . 'x'
            . $this->height . $this->extension;

        /**
         * Check to see if the requested image exists
         */
        if ($this->_imageExists() === false) {
            return;
        }

        /**
         * Check to see if the requested image is cached, if it is, then
         * echo it to the page and return
         */
        $cached = $this->_checkCache();
        if ($cached !== false) {
            $this->file = $cached;
            return;
        }

        /**
         * No cached image exists, create it
         *
         * Check to see if the image is a jpg or png
         */
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
        default:
            return;
        }

        /**
         * Create new image resource
         */
        $newImage = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled(
            $newImage, $image, 0, 0, 0, 0, $this->width,
            $this->height, imagesx($image), imagesy($image)
        );
        /**
         * Save the new image as the correct filetype
         */
        switch($this->extension) {
        case '.png':
            imagepng($newImage, 'cache/' . $this->cachedFilename);
            break;
        case '.jpg':
        case '.jpeg':
            imagejpeg($newImage, 'cache/' . $this->cachedFilename);
            break;
        }

        $this->file = 'cache/' . $this->cachedFilename;
        return;
    }

    /**
     * Checks to see if the requested file is cached and if it is
     * then it checks to see if it is newer that the original
     * image, if it isn't then it deletes the cached version of
     * the image
     * @return bool|string
     */
    private function _checkCache()
    {
        /**
         * Check to see if the cache folder exists, if not, create it
         * and then return false because the cached image can't yet
         * exist
         */
        if (is_dir('cache') === false) {
            mkdir('cache');
            return false;
        }
        /**
         * Check to see if the requested file exists, if it does, then
         * return it's full path else return false
         */
        if (file_exists('cache/' . $this->cachedFilename)) {
            /**
             * Check to see if original is newer than the cache image, if it is,
             * assume the image has been changed so delete the cached version
             * and return false
             */
            if (filemtime('cache/' . $this->cachedFilename) < filemtime($this->imageDirectory . $this->filename . $this->extension)) {
                unlink('cache/' . $this->cachedFilename);
                return false;
            }
            return 'cache/' . $this->cachedFilename;
        } else {
            return false;
        }
    }

    /**
     * Check to see if the requested file exists
     * @return bool
     */
    private function _imageExists()
    {
        return file_exists(
            $this->imageDirectory . $this->filename . $this->extension
        );
    }
}
