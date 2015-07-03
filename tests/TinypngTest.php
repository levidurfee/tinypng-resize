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
        #echo "API KEY" . $apikey;
        $this->tinypng = new Tinypng($apikey);
    }

    /**
     * Destroy
     */
    protected function tearDown()
    {
        $this->tinypng = null;
    }

    public function testShrinkSuccess()
    {
        $result = $this->tinypng->shrink('ignore/helicopter-original.png',
            'ignore/helicopter-new.png')->resize(150, 150, true);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Credentials are invalid
     */
    public function testInvalidCredentials()
    {
        $tp = new Tinypng('invalidcredentials');
        $result = $tp->shrink('ignore/helicopter-original.png',
            'ignore/helicopter-new.png')->resize(150, 150, true);
        $this->assertFalse($result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Width or height must be set and be an int
     */
    public function testInvalidWidth()
    {
        $result = $this->tinypng->shrink('ignore/helicopter-original.png',
            'ignore/helicopter-new.png')->resize('', 150, true);
        $this->assertFalse($result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Width or height must be set and be an int
     */
    public function testInvalidHeight()
    {
        $result = $this->tinypng->shrink('ignore/helicopter-original.png',
            'ignore/helicopter-new.png')->resize(150, '', true);
        $this->assertFalse($result);
    }
}
