<?php

namespace Alc\Addic7edCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProxyTestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('test:proxy')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = array(
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0',
                // 'Referer' => 'http://www.addic7ed.com'
            ),
            // 'connect_timeout' => 15,
            // 'timeout' => 30,
            'proxy' => 'socks5://localhost:9050',
        );

        $client = new \GuzzleHttp\Client($options);

        $url = 'http://httpbin.org/ip';

        $request = $client->request('GET', $url);
        $data = $request->getBody()->getContents();

        echo $data;
    }
}
