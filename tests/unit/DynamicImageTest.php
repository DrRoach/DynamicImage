<?php 
use DynamicImage\DynamicImage;

class DynamicImageTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $testData = [
        'width' => 500,
        'height' => 500,
        'filename' => 'deadpool.jpg',
        'exceptions' => true,
        'debug' => false,
        'invalidate_cache' => 1,
        'image_directory' => 'images/'
    ];

    protected function _before()
    {
        require_once __DIR__ . '/../../src/DynamicImage.php';
    }

    protected function _after()
    {
    }

    /**
     *  @group main
     */
    public function testFull()
    {
        $di = new DynamicImage($this->testData);
    }

    /**
     *  @group main
     */
    public function testMissingParams()
    {
        // Make sure by the end of the test error has been set to true
        $error = false;

        try {
            // Unset the height parameter that is required
            $data = $this->testData; 
            unset($data['height']);
            $di = new DynamicImage($data);
        } catch(Exception $e) {
            $error = true;
            $this->assertEquals(DynamicImage::$_ERRORS['MISSING_PARAM']['code'], $e->getCode());
        }

        $this->assertTrue($error);
    }

    /**
     *  @group main
     */
    public function testImageMissing()
    {
        // Make sure by the end of the test error has been set to true
        $error = false;

        try {
            $data = $this->testData;
            $data['filename'] = 'missing.jpg';
            $di = new DynamicImage($data);
        } catch(Exception $e) {
            $error = true;
            $this->assertEquals(DynamicImage::$_ERRORS['IMAGE_MISSING']['code'], $e->getCode());
        }

        $this->assertTrue($error);
    }

    /**
     *  @group main
     */
    public function testMissingImageReturn()
    {
        $data = $this->testData;
        $data['image_missing'] = 'triangle.jpg';
        $data['filename'] = 'missing.jpg';
        $di = new DynamicImage($data);
        $this->assertEquals('cache/triangle-500x500.jpg', $di->file);
    }

    /**
     *  @group main
     */
    public function testUnsupportedExtension()
    {
        // Make sure by the end of the test error has been set to true
        $error = false;

        $data = $this->testData;
        $data['filename'] = 'invalid.tiff';

        try {
            $di = new DynamicImage($data);
        } catch(Exception $e) {
            $error = true;
            $this->assertEquals(DynamicImage::$_ERRORS['UNSUPPORTED_EXTENSION']['code'], $e->getCode());
        }

        $this->assertTrue($error);
    }

    /**
     *  @group cache
     */
    public function testCreatingCache()
    {
        // Make sure by the end of the test error has been set to true
        $error = false;

        // Directory of the cache
        $cacheDir = __dir__ . '/../../cache';

        // If the cache directory exists then delete it
        if (is_dir($cacheDir)) {
            // Delete all image file that are in the cache directory
            foreach (glob($cacheDir . "/*") as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            rmdir($cacheDir);
        }
        
        $data = $this->testData;

        // Create new object and make sure that cache is created
        $di = new DynamicImage($data);

        $this->assertTrue(is_dir($cacheDir));
    }

    public function testInvalidWidthOrHeight()
    {
        $data = $this->testData;

        $data['width'] = 'abc';
        $error = false;

        try {
            $di = new DynamicImage($data);
        } catch (Exception $e) {
            $error = true;
            $this->assertEquals(DynamicImage::$_ERRORS['INTEGER_REQUIRED']['code'], $e->getCode());
        }

        $this->assertTrue($error);
    }

    public function testJpgImageCreation()
    {
        $data = $this->testData;

        $di = new DynamicImage($data);

        $this->assertEquals(IMAGETYPE_JPEG, exif_imagetype($di->file));
    }

    public function testPngImageCreation()
    {
        $data = $this->testData;

        $data['filename'] = "mario.png";

        $di = new DynamicImage($data);

        $this->assertEquals(IMAGETYPE_PNG, exif_imagetype($di->file));
    }

    public function testGifImageCreation()
    {
        $data = $this->testData;

        $data['filename'] = "surf.gif";

        $di = new DynamicImage($data);

        $this->assertEquals(IMAGETYPE_GIF, exif_imagetype($di->file));
    }
}
