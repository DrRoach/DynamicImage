<?php

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
    // If an error has occured then this will be set to true
    public $error = false;

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
    // Flag to skip image validation and just load it straight from cache if it exists
    private $validateImage = true;
    // Cache invalidation in seconds. If null then don't validate
    private $invalidateSeconds = null;

    // Setting to determine whether or not exceptions should be thrown
    private $exceptions = false;
    // Array that contains all of the error codes and messages
    private $_ERRORS = [
        'MISSING_PARAM' => [
            'code' => 1,
            'message' => 'You are missing one of the required parameters.'
        ],
        'IMAGE_MISSING' => [
            'code' => 2,
            'message' => 'The image that you requested to generate could not be found.'
        ],
        'UNSUPPORTED_EXTENSION' => [
            'code' => 3,
            'message' => 'The filetype that you gave isn\'t supported.'
        ],
        'CREATING_CACHE' => [
            'code' => 4,
            'message' => 'There was a problem when creating the `cache` directory. Please create it manually.'
        ],
        'INVALID_PERMISSIONS' => [
            'code' => 5,
            'message' => 'The cache folder cannot be written to. Please make sure that it has the correct permissions.'
        ]
    ];

    /**
     * First function that is called and also the function that calls
     * all other parts of this file.
     * 
     * User supplies array of parameters.
     */
    function __construct(Array $params = [])
    {
        // If `debug` is set to true in the parameters, turn on errors
        if (!empty($params['debug']) && $params['debug'] == true) {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        // If `exceptions` is set in parameters then set it for this class
        if (!empty($params['exceptions'])) {
            $this->exceptions = $params['exceptions'];
        }

        /**
         * Error checking and handling
         */
        if (empty($params['filename']) || empty($params['width'])
            || empty($params['height'])
        ) {
            $this->error($this->_ERRORS['MISSING_PARAM']['message'], $this->_ERRORS['MISSING_PARAM']['code']);
            return;
        }

        /**
         * Store the filename, width, height and image directory and then check the
         * cache to see if this file already exists
         */
        //Don't allow '..' to prevent directory traversal
        $cleanFilename = str_replace('..', '', $params['filename']);
        $this->filename = $this->getFilename($cleanFilename);
        $this->extension = $this->getExtension($cleanFilename);

        $this->width = $params['width'];
        $this->height = $params['height'];

        /**
         * This MUST end with a trailing slash
         */
        if (!empty($params['imageDirectory'])) {
            //Don't allow '..' to prevent directory traversal
            $this->imageDirectory = str_replace('..', '', $params['imageDirectory']);
        }

        // Check to see if `invalidate_cache` has been set and if it has store it
        if (!empty($params['invalidate_cache'])) {
            $this->invalidateSeconds = $params['invalidate_cache'];
        }

        $this->cachedFilename = $this->filename . '-' . $this->width . 'x'
            . $this->height . $this->extension;

        // If the user has set `validate_image` to false then check the cache straight away
        if (!empty($params['validate_image']) && $params['validate_image'] == false) {
            $this->validate_image = false;
            // Check the cache for the image but don't validate it
            $cached = $this->checkCache($validate = false);
            if ($cached !== false) {
                $this->file = $cached;
                return;
            }
        }

        /**
         * Check to see if the requested image exists
         */
        if ($this->_imageExists() === false) {
            // Check to see if `image_missing` is set
            if (!empty($params['image_missing'])) {
                // Requested image couldn't be found. Instead load the `image_missing` one.
                
                $cleanFilename = str_replace('..', '', $params['image_missing']);
                $this->filename = $this->getFilename($cleanFilename);
                $this->extension = $this->getExtension($cleanFilename);
            } else {
                $this->error($this->_ERRORS['IMAGE_MISSING']['message'], $this->_ERRORS['IMAGE_MISSING']['code']);
                return;
            }
        }

        /**
         * Check to see if the requested image is cached, if it is, then
         * echo it to the page and return. We only want to run this if `validate_image` was set to
         * true, hence the if block. If it is set to false then the cache has already been checked.
         */
        if ($this->validateImage) {
            $cached = $this->_checkCache();
            if ($cached !== false) {
                $this->file = $cached;
                return;
            }
        } 

        // Check to make sure that the cache is writable before trying to save to it
        if (!is_writable('cache')) {
            $this->error($this->_ERRORS['INVALID_PERMISSIONS']['message'], $this->_ERRORS['INVALID_PERMISSIONS']['code']);
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
            $this->error($this->_ERRORS['UNSUPPORTED_EXTENSION']['message'], $this->_ERRORS['UNSUPPORTED_EXTENSION']['code']);
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
     * Get the name of the file from it's whole path
     *
     * @param $file - The whole file path.
     *
     * @return $filename - The name of the file taken from the whole file path.
     */
    private function getFilename($file) {
        $finalPeriod = strrpos($file, '.');
        return substr($file, 0, $finalPeriod);
    }

    /**
     * Get the image extension from the path that's been supplied
     *
     * @param $file - The whole requested file path
     *
     * @return $extension - The requested files' extension
     */
    private function getExtension($file) {
        $finalPeriod = strrpos($file, '.');
        return substr($file, $finalPeriod);
    }

    /**
     * Checks to see if the requested file is cached and if it is
     * then it checks to see if it is newer that the original
     * image, if it isn't then it deletes the cached version of
     * the image
     *
     * @param $validate Whether or not to validate that the original image hasn't been updated since cache file was made
     *
     * @return bool|string
     */
    private function _checkCache($validate = true)
    {
        /**
         * Check to see if the cache folder exists, if not, create it
         * and then return false because the cached image can't yet
         * exist
         */
        if (is_dir('cache') === false) {
            // If cache is writable and the cache directory is made return false
            if (is_writable('cache') && mkdir('cache')) {
                return false;
            } else {
                // Cache dir couldn't be made so display error
                $this->error($this->_ERRORS['CREATING_CACHE']['message'], $this->_ERRORS['CREATING_CACHE']['code']);
                return;
            }
        }

        /**
         * Check to see if the requested file exists, if it does, then
         * return it's full path else return false
         */
        if (file_exists('cache/' . $this->cachedFilename)) {
            $date = new DateTime();

            /**
             * Check to see if original is newer than the cache image, as long as validate is set to true. 
             * If it is, assume the image has been changed so delete the cached version
             * and return false
             */
            if ($validate == true && filemtime('cache/' . $this->cachedFilename) < filemtime($this->imageDirectory . $this->filename . $this->extension)) {
                // Image has been updated so make sure that we re-generate it
                unlink('cache/' . $this->cachedFilename);
                return false;
            } else if (!is_null($this->invalidateSeconds) && (filemtime('cache/' . $this->cachedFilename) + $this->invalidateSeconds) < $date->getTimestamp()) {
                var_dump('IN HERE NOW');
                // Cached image has passed validation time so re-generate it
                unlink('cache/' . $this->cachedFilename);
                return false;
            }

            // Else return the cached image
            return 'cache/' . $this->cachedFilename;
        } else {
            // File doesn't exist in the cache
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

    private function error($message, $code = 0) {
        $this->error = true;

        // Only throw the exception if exceptions have been set to true
        if ($this->exceptions) {
            throw new Exception($message, $code);
        }
    }
}
