<?php namespace teklife;
define("DS", DIRECTORY_SEPARATOR);

require_once(dirname(__DIR__) . DS . 'vendor' . DS . 'autoload.php');

class TinypngTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var tinypng
     */
    private $tinypng;

    /**
     * Setup
     */
    protected function setUp()
    {
        if(file_exists(dirname(__DIR__) . DS . 'config.php')) {
            require(dirname(__DIR__) . DS . 'config.php');
        } else {
            $apikey = getenv('TP_API_KEY');
        }
        $this->tinypng = new Tinypng($apikey);
    }

    /**
     * Destroy
     */
    protected function tearDown()
    {
        $this->tinypng = null;
    }

    /**
     * Test successfully shrink / compressing an image
     */
    public function testShrinkSuccess()
    {
        try {
            $result = $this->tinypng->shrink('ignore/helicopter-original.png',
            'ignore/helicopter-new.png')->resize(150, 150, true);
            $this->assertTrue($result);
        } catch(\Exception $e) {
            $this->assertEquals('Your monthly limit has been exceeded', $e->getMessage());
        }
    }

    /**
     * Test invalid api key
     */
    public function testInvalidCredentials()
    {
        try {
            $tp = new Tinypng('invalidcredentials');
            $result = $tp->shrink('ignore/helicopter-original.png',
                'ignore/helicopter-new.png')->resize(150, 150, true);
            $this->assertFalse($result);
        } catch(\Exception $e) {
            $this->assertEquals('Credentials are invalid', $e->getMessage());
        }
    }

    /**
     * Test passing an invalid width
     */
    public function testInvalidWidth()
    {
        try {
            $result = $this->tinypng->shrink('ignore/helicopter-original.png',
                'ignore/helicopter-new.png')->resize('', 150, true);
            $this->assertFalse($result);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('Width or height must be set and be an int', $e->getMessage());
        } catch(\Exception $e) {
            $this->assertEquals('Your monthly limit has been exceeded', $e->getMessage());
        }
    }

    /**
     * Test passing an invalid height
     */
    public function testInvalidHeight()
    {
        try {
            $result = $this->tinypng->shrink('ignore/helicopter-original.png',
                'ignore/helicopter-new.png')->resize(150, '', true);
            $this->assertFalse($result);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('Width or height must be set and be an int', $e->getMessage());
        } catch(\Exception $e) {
            $this->assertEquals('Your monthly limit has been exceeded', $e->getMessage());
        }
    }

    /**
     * test invalid file
     */
    public function testShrinkInvalidFile()
    {
        try {
            $result = $this->tinypng->shrink('VERSION', 'VERSION');
            $this->assertFalse($result);
        } catch(\Exception $e) {
            $this->assertEquals('Error compressing the file.', $e->getMessage());
        }
    }
}
