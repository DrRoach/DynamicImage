<?php 
use DynamicImage\Validate;
use DynamicImage\DynamicImage;
use DynamicImage\Exceptions\MissingParamException;
use DynamicImage\Exceptions\MissingImageException;

class ValidateTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        require_once __DIR__ . '/../../src/Validate.php';
    }

    protected function _after()
    {
    }

    public function testInput()
    {
        try {
            $di = new DynamicImage([]);
        } catch (MissingParamException $e) {
            $this->assertEquals("Missing parameters", $e->getMessage());
            $this->assertEquals(501, $e->getCode());
        }

        try {
            $di = new DynamicImage([
                'height' => 500
            ]);
        } catch (MissingParamException $e) {
            $this->assertEquals("Missing width", $e->getMessage());
            $this->assertEquals(501, $e->getCode());
        }

        try {
            $di = new DynamicImage([
                'width' => 500
            ]);
        } catch (MissingParamException $e) {
            $this->assertEquals("Missing height", $e->getMessage());
            $this->assertEquals(501, $e->getCode());
        }
    }

    public function testMissingImage()
    {
        $di = new DynamicImage([
            'width' => 500,
            'height' => 500
        ]);

        try {
            $di->generate('nosuchimageexists.jog');
        } catch (MissingImageException $e) {
            $this->assertEquals("Image notsuchimageexists.jpg does not exist", $e->getMessage());
            $this->assertEquals(502, $e->getCode());
        }
    }
}
