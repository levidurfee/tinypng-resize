<?php namespace teklife;

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
        $this->tinypng = new Tinypng;
    }

    /**
     * Destroy
     */
    protected function tearDown()
    {
        $this->tinypng = null;
    }
}
