<?php 
use DynamicImage\DynamicImage;

class GenerateTest extends PHPUnit\Framework\TestCase
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

    public function testGenerateJpg()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $di = new DynamicImage([
            'width' => 500,
            'height' => 500
        ]);

        $images = $di->generate('/images/deadpool.jpg');

        $this->assertEquals([
            '/images/deadpool.jpg' => "/../cache/f4824a4c584e638e26573e0e469d64e5.jpg"
        ], $images['images']);
    }

    public function testGeneratePng()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $di = new DynamicImage([
            'width' => 500,
            'height' => 500
        ]);

        $images = $di->generate('/images/mario.png');

        $this->assertEquals([
            '/images/mario.png' => "/../cache/010282a0b0a362bc9d5859eb7d01be12.png"
        ], $images['images']);
    }

    public function testGenerateGif()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $di = new DynamicImage([
            'width' => 500,
            'height' => 500
        ]);

        $images = $di->generate('/images/surf.gif');

        $this->assertEquals([
            '/images/surf.gif' => "/../cache/ad9bf70be2552bb94ca8ecdce817d301.gif"
        ], $images['images']);
    }
}
