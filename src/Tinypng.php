<?php namespace teklife;

const VERSION = '0.5.0';

/**
 * @author Levi <levi.durfee@gmail.com>
 * @version 0.5.0
 */
class Tinypng {

    protected $host = 'https://api.tinypng.com/shrink';
    protected $apikey;
    protected $input;
    protected $output;
    protected $width;
    protected $height;
    protected $fit;
    protected $jsonRequest;

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
    }

    public function shrink($input, $output, $width = '', $height = '', $fit = false)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->width  = $width;
        $this->height = $height;
        $this->fit    = $fit;

        if(function_exists('curl_version')) {
            $this->curlShrink();
        } else {
            $this->fopenShrink();
        }
        return true;
    }

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
                        'content' => self::returnInput()
                ),
                'ssl' => array(
                    'cafile'      => self::caBundle(),
                    'verify_peer' => true
                )
        );

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

        $result = fopen($this->host, 'r', false, stream_context_create($options));

        if($result) {
            foreach($http_response_header as $header) {
                if (strtolower(substr($header, 0, 10)) === "location: ") {
                    $resizeUrl = substr($header, 10);
                }
            }
        } else {
            #echo 'error';
        }

        $image = file_get_contents($resizeUrl, false, stream_context_create($resizeOption));
        file_put_contents($this->output, $image);
    }

    protected function curlShrink()
    {
        # initialize curl
        $request = curl_init();

        # set my options for curl
        curl_setopt_array($request, array(
            CURLOPT_URL            => $this->host,
            CURLOPT_USERPWD        => 'api:' . $this->apikey,
            CURLOPT_POSTFIELDS     => self::returnInput(),
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_CAINFO         => self::caBundle(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => self::userAgent()
        ));

        # get the response
        $response = curl_exec($request);

        # check the response
        if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {
            $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
            foreach (explode("\r\n", $headers) as $header) {
                if (strtolower(substr($header, 0, 10)) === "location: ") {
                    $this->makeJson();
                    $request = curl_init();
                    curl_setopt_array($request, array(
                        CURLOPT_URL            => substr($header, 10),
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

                    if(file_put_contents($this->output, curl_exec($request))) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            print(curl_error($request));
            #throw new \Exception('Error compressing the file');
        }
    }

    protected function makeJson()
    {
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
            } else {
                throw new \Exception('Width or height must be set and be an int');
            }
            $this->jsonRequest = json_encode($jsonArray, true);
            return true;
        }
    }

    private static function caBundle()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cacert.pem';
    }

    private static function userAgent()
    {
        return 'levidurfee/tinypng/' . VERSION . ' PHP/' . PHP_VERSION;
    }

    private static function apiAuth()
    {
        return base64_encode('api:' . $this->apikey);
    }

    private static function returnInput()
    {
        return file_get_contents($this->input);
    }
}
