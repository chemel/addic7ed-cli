<?php

namespace Alc\Addic7edCli\Component;

class HttpClient
{
    private $proxy;

    /**
     * Set proxy
     *
     * @param string proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Get HTTP client
     *
     * @return \GuzzleHttp\Client client
     */
    public function getClient()
    {
        $options = array(
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0',
                'Referer' => 'http://www.addic7ed.com'
            ),
            // 'connect_timeout' => 15,
            // 'timeout' => 30,
        );

        if ($this->proxy) {
            $options['proxy'] = $this->proxy;
        }

        return new \GuzzleHttp\Client($options);
    }
}
