<?php
namespace MHC\FSAPI;

class Manager
{
    protected $api_key = null;
    protected $host    = null;

    CONST SUFFIX_PUT = '/put';
    CONST SUFFIX_GET = '/get/';

    /**
     * Manager constructor.
     * @param null $api_key
     */
    public function __construct($api_key = null, $host = null)
    {
        if ($api_key == null) {
            $cConfig = new Config();
            $config  = $cConfig->getConfig();
            $api_key = $config['api_key'];
        }
        if ($host == null) {
            if (!isset($config)) {
                $cConfig = new Config();
                $config  = $cConfig->getConfig();
            }
            $host = $config['host'];
        }

        $host = rtrim($host,'/');
        if(substr($host,0,4) != 'http') {
            $host = 'http://'.$host;
        }

        $this->api_key = $api_key;
        $this->host    = $host;
    }

    /**
     * @param string $name
     * @param string $location
     * @param null $type
     * @param null $size
     * @return mixed
     * @throws \Exception
     */
    public function putFile($name, $location, $type = null, $size = null)
    {
        if (!file_exists($location)) {
            throw new \Exception('File does not exist at the specified location');
        }

        $post = [
            'name'  => $name,
            'type'  => $type,
            'size'  => $size,
            'file'  => '@'.$location
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host.self::SUFFIX_PUT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Service-Token: '.$this->api_key
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param $fileToken
     * @return mixed
     */
    public function getFile($fileToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host.self::SUFFIX_GET.$fileToken);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Service-Token: '.$this->api_key
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


}
