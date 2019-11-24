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
     * @group main
     *
     * When passing images to `generate()` we should be able to pass either an array or a string.
     *  If a string is passed we need to make sure that it is converted into an array before
     *  processing
     */
    public function testReformatImageInput()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $width = 500;
        $height = 500;

        $di = new DynamicImage([
            'width' => $width,
            'height' => $height
        ]);

        $images = $di->reformatImagesInput('deadpool.jpg');

        $this->assertEquals([
            'deadpool.jpg'
        ], $images);

        $di = new DynamicImage([
            'width' => $width,
            'height' => $height
        ]);

        $images = $di->reformatImagesInput([
            'triangle.jpg',
            'deadpool.jpg'
        ]);

        $this->assertEquals([
            'triangle.jpg',
            'deadpool.jpg'
        ], $images);
    }

    /**
     *  @group main
     *
     *  The only required params when creating the object are width and height
     */
    public function testMissingParams()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $width = 500;
        $height = 500;

        $di = null;
        try {
            $di = new DynamicImage([
                'width' => $width
            ]);
        } catch(\DynamicImage\Exceptions\MissingParamException $e) {
            $this->assertEquals("Missing height", $e->getMessage());
        }

        try {
            $di = new DynamicImage([
                'height' => $height
            ]);
        } catch (\DynamicImage\Exceptions\MissingParamException $e) {
            $this->assertEquals("Missing width", $e->getMessage());
        }
    }

    /**
     *  @group main
     */
    public function testImageMissingWithFalsePlaceholderImage()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $width = 500;
        $height = 500;
        $missingImageName = "imagethatdoesnotexist.jpg";

        $di = new DynamicImage([
            'width' => $width,
            'height' => $height
        ]);

        $di->setMissingPlaceholderImage(false);

        try {
            $di->generate($missingImageName);
        } catch (\DynamicImage\Exceptions\MissingImageException $e) {
            $this->assertEquals("The image {$missingImageName} does not exist", $e->getMessage());
        }

        $di->setMissingPlaceholderImage("/images/missing.jpg");

        $di->generate($missingImageName);
    }

    /**
     *  @group main
     */
    public function testMissingImageWithPlaceholder()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $width = 500;
        $height = 500;
        $missingImageName = "imagethatdoesnotexist.jpg";

        $di = new DynamicImage([
            'width' => $width,
            'height' => $height
        ]);

        $images = $di->generate($missingImageName);

        $this->assertEquals([
            $missingImageName => "/../cache/bb420250537a0213179d0e7bfddc8649.jpg"
        ], $images['images']);

        $this->assertEquals([
            "Image {$missingImageName} does not exist."
        ], $images['errors']);
    }

    /**
     * @group main
     */
    public function testFullGenerate()
    {
        `rm -rf images/cache && rm -rf cache && mkdir images/cache && mkdir cache`;

        $width = 500;
        $height = 500;

        $di = new DynamicImage([
            'width' => $width,
            'height' => $height
        ]);

        $images = $di->generate('/images/deadpool.jpg');

        $this->assertEquals([
            '/images/deadpool.jpg' => "/../cache/f4824a4c584e638e26573e0e469d64e5.jpg"
        ], $images['images']);
    }
}
