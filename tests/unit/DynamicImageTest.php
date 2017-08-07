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

    // tests
    public function testFull()
    {
        $di = new DynamicImage($this->testData);
    }

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
            $this->assertEquals('You are missing one of the required parameters.', $e->getMessage());
        }

        $this->assertTrue($error);
    }

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
            $this->assertEquals('The image that you requested to generate could not be found.', $e->getMessage());
        }

        $this->assertTrue($error);
    }

    public function testMissingImageReturn()
    {
        $data = $this->testData;
        $data['image_missing'] = 'triangle.jpg';
        $data['filename'] = 'missing.jpg';
        $di = new DynamicImage($data);
        $this->assertEquals('triangle-500x500.jpg', $di->file);
    }
}
