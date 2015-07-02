<?php namespace teklife;

/**
 * @author Levi <levi.durfee@gmail.com>
 * @version 0.1.0
 */
class Tinypng {
    protected $host = "api.tinypng.com";

    public function __construct()
    {
        echo function_exists('curl_version');
    }
}
