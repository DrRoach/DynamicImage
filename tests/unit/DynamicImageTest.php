<?php


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
        'invalidate_cache' => 0
    ];

    protected function _before()
    {
        require_once __DIR__ . '/../../DynamicImage.php';
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
            $this->assertEquals(1, $e->getCode());
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
            $this->assertEquals(2, $e->getCode());
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
        $data['filename'] = 'surf.gif';

        try {
            $di = new DynamicImage($data);
        } catch(Exception $e) {
            $error = true;
            $this->assertEquals(3, $e->getCode());
        }

        $this->assertTrue($error);
    }

    /**
     *  @group main
     */
    public function testImagePermissions()
    {
        // Make sure by the end of the test error has been set to true
        $error = false;

        $data = $this->testData;

        // Create test image file
        try {
            $fp = fopen(__DIR__ . '/../../test.jpg', 'w');
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        // Set incorrect permissions for test file
        chmod(__DIR__ . '/../../test.jpg', 600);

        $data['filename'] = 'test.jpg';

        try {
            $di = new DynamicImage($data);
        } catch (Exception $e) {
            $error = true;
            $this->assertEquals(6, $e->getCode());
        }

        $this->assertTrue($error);

        unlink(__DIR__ . '/../../test.jpg');

        shell_exec("mkdir cache");
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

            // Delete the cache folder
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
            $this->assertEquals($e->getCode(), 7);
        }

        $this->assertTrue($error);
    }
}
