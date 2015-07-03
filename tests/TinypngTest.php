<?php namespace teklife;
define("DS", DIRECTORY_SEPARATOR);
if(file_exists(dirname(__DIR__) . DS . 'config.php')) {
    require_once(dirname(__DIR__) . DS . 'config.php');
    $apikey = TP_API_KEY;
} else {
    $apikey = getenv('TP_API_KEY');
}
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
            'ignore/helicopter-new.png', 150);
        $this->assertTrue($result);
    }
}
