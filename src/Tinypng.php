<?php namespace teklife;

const VERSION = '0.8.2';

/**
 * @author Levi <levi.durfee@gmail.com>
 * @version 0.8.2
 */
class Tinypng {

    protected $host = 'https://api.tinypng.com/shrink';
    protected $apikey;
    protected $input;
    protected $output;
    protected $width;
    protected $height;
    protected $fit;
    protected $resizeUrl;
    protected $jsonRequest;
    protected $cover;

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
    }

    /**
     * Input filename image, and output filename
     * @param $input
     * @param $output
     * @return $this
     */
    public function shrink($input, $output)
    {
        # Set these so they can be used throughout the class
        $this->input  = $input;
        $this->output = $output;

        # Check and see if curl is installed - if it isn't, use fopen
        if(function_exists('curl_version')) {
            $this->curlShrink();
        } else {
            $this->fopenShrink();
        }
        return $this;
    }

    /**
     * Resize the image, fit will keep it within the dimensions you set
     * @param string $width
     * @param string $height
     * @param bool|false $fit
     * @return bool
     */
    public function resize($width = '', $height = '', $fit = false, $cover = false)
    {
        # Set these so they can be used throughout the class
        $this->width  = $width;
        $this->height = $height;
        $this->fit    = $fit;
        $this->cover  = $cover;

        # Check and see if curl is installed - if it isn't, use fopen
        if(function_exists('curl_version')) {
            return $this->curlResize();
        } else {
            return $this->fopenResize();
        }
    }

    /**
     * use curl to shrink / compress the image
     * @return bool
     * @throws \Exception
     */
    protected function curlShrink()
    {
        # initialize curl
        $request = curl_init();

        # set my options for curl
        curl_setopt_array($request, array(
            CURLOPT_URL            => $this->host,
            CURLOPT_USERPWD        => 'api:' . $this->apikey,
            CURLOPT_POSTFIELDS     => file_get_contents($this->input),
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_CAINFO         => self::caBundle(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => self::userAgent()
        ));

        # get the response
        $response = curl_exec($request);

        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
        self::parseHeaders(substr($response, 0, $headerSize));
        $body = substr($response, $headerSize);

        $this->handleStatus($status, $body);

        # check the response
        if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {
            $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
            foreach (explode("\r\n", $headers) as $header) {
                if (strtolower(substr($header, 0, 10)) === "location: ") {
                    $this->resizeUrl = substr($header, 10);
                }
            }
            if(file_put_contents($this->output, file_get_contents($this->resizeUrl))) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception('Error compressing the file.');
            return false;
        }
    }

    /**
     * Use curl to resize the image
     * @return bool
     * @throws \Exception
     */
    protected function curlResize()
    {
        $this->makeJson();
        $request = curl_init();
        curl_setopt_array($request, array(
            CURLOPT_URL            => $this->resizeUrl,
            CURLOPT_USERPWD        => 'api:' . $this->apikey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $this->jsonRequest,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($this->jsonRequest)
            ),
            CURLOPT_CAINFO         => self::caBundle(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => self::userAgent()
        ));

        $response = curl_exec($request);

        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
        self::parseHeaders(substr($response, 0, $headerSize));
        $body = substr($response, $headerSize);

        $this->handleStatus($status, $body);

        if(file_put_contents($this->output, $response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Use fopen to shrink / compress the image
     */
    protected function fopenShrink()
    {
        $mimeType = image_type_to_mime_type(exif_imagetype($this->input));

        # setup array for my options
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-type: ' . $mimeType,
                    'Authorization: Basic ' . self::apiAuth()
                ),
                'content' => file_get_contents($this->input)
            ),
            'ssl' => array(
                'cafile'      => self::caBundle(),
                'verify_peer' => true
            )
        );

        $result = fopen($this->host, 'r', false, stream_context_create($options));

        if($result) {
            foreach($http_response_header as $header) {
                if (strtolower(substr($header, 0, 10)) === "location: ") {
                    $this->resizeUrl = substr($header, 10);
                    file_put_contents($this->output, fopen(substr($header, 10), "rb", false));
                }
            }
        } else {
            #echo 'error';
        }
    }

    /**
     * Use fopen to resize the image
     * @return bool
     */
    protected function fopenResize()
    {
        $this->makeJson();

        $resizeOption = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-type: application/json',
                    'Authorization: Basic ' . self::apiAuth()
                ),
                'content' => $this->jsonRequest
            ),
            'ssl' => array(
                'cafile'      => self::caBundle(),
                'verify_peer' => true
            )
        );

        $image = file_get_contents($this->resizeUrl, false, stream_context_create($resizeOption));
        file_put_contents($this->output, $image);

        return true;
    }

    /**
     * Create the json resize request
     * @return bool
     */
    protected function makeJson()
    {
        #echo "Width: " . $this->width . "\r\n";
        #echo "Height: " . $this->height . "\r\n";
        if(!(is_int($this->width)) OR (!is_int($this->height))) {
            throw new \InvalidArgumentException('Width or height must be set and be an int');
            return false;
        }
        if((strlen($this->width)) == 0 OR (strlen($this->height) == 0)) {
            throw new \InvalidArgumentException('Width or height must be set and be an int');
            return false;
        }
        if($this->cover){

            $jsonArray = array('resize' => array('width' => $this->width, 'height' => $this->height, 'method' => 'cover'));
            $this->jsonRequest = json_encode($jsonArray, true);
            return true;

        } else {

            if($this->fit) {
                list($width, $height) = getimagesize($this->input);
                if($width > $height) {
                    $jsonArray = array('resize' => array('width' => $this->width));
                } else {
                    $jsonArray = array('resize' => array('height' => $this->height));
                }
                $this->jsonRequest = json_encode($jsonArray, true);
                return true;
            } else {
                if(is_int($this->width)) {
                    $jsonArray = array('resize' => array('width' => $this->width));
                } elseif(is_int($this->height)) {
                    $jsonArray = array('resize' => array('height' => $this->height));
                }
                $this->jsonRequest = json_encode($jsonArray, true);
                return true;
            }

        }


    }

    /**
     * Return the cabundle
     * @return string
     */
    private static function caBundle()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cacert.pem';
    }

    /**
     * Send a 'user-agent' to TinyPNG for them to help debug
     * @return string
     */
    private static function userAgent()
    {
        return 'levidurfee/tinypng/' . VERSION . ' PHP/' . PHP_VERSION;
    }

    /**
     * Return the api auth base64 encoded
     * @return string
     */
    protected function apiAuth()
    {
        return base64_encode('api:' . $this->apikey);
    }

    /**
     * Parse the headers sent by the TinyPNG server
     * @param $headers
     * @return array
     */
    protected static function parseHeaders($headers) {
        if (!is_array($headers)) {
            $headers = explode("\r\n", $headers);
        }
        $res = array();
        foreach ($headers as $header) {
            $split = explode(":", $header, 2);
            if (count($split) === 2) {
                $res[strtolower($split[0])] = trim($split[1]);
            }
        }
        return $res;
    }

    /**
     * Handle the status response from the server and throw exception if error occurs
     * @param $status
     * @param $body
     * @return bool
     * @throws \Exception
     */
    protected function handleStatus($status, $body)
    {
        $errors = array(401, 429);
        if(in_array($status, $errors)) {
            $bodyDecoded = json_decode($body);
            throw new \Exception($bodyDecoded->message);
            return false;
        }
    }
}
