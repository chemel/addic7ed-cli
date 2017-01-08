<?php

namespace Alc\Addic7edCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Alc\Addic7edCli\Component\FilenameParser;
use Alc\Addic7edCli\Component\HttpClient;
use Alc\Addic7edCli\Database\Addic7edDatabase;

class Addic7edCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('get')
            ->setDescription('Download subtitle from addic7ed')
            ->addArgument('input', InputArgument::OPTIONAL, 'The input file or search pattern.', '/\.(mkv|mp4|webm|avi|mpg|mpeg|wmv|3gp)$/i')
            ->addOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Language of the subtitle.', 'French')
            ->addOption('erase', 'e', InputOption::VALUE_OPTIONAL, 'Erase existing subtitle.', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $finder = new Finder();
        $finder
            ->files()
            ->in('.')
            ->name($input->getArgument('input'))
            ->sortByName()
        ;

        $client = new HttpClient();
        $client = $client->getClient();

        $database = new Addic7edDatabase($client);

        $language = $input->getOption('lang');

        foreach ($finder as $file) {
            $output->writeln("\n".'<info>[INFO]</info> Filename: '.$file->getFilename());

            $subFilename = $file->getBasename($file->getExtension()).'srt';
            $subFullpath = $file->getPath().'/'.$subFilename;

            if (!$input->getOption('erase') && file_exists($subFullpath)) {
                $output->writeln('<info>[INFO]</info> Subtitle exist skipping.');
                continue;
            }

            $filenameParser = new FilenameParser($file->getFilename());

            $results = $database->find($filenameParser->getTitle(), $language, $filenameParser->getSeason(), $filenameParser->getEpisode());

            $table = array();
            $urls = array();
            $id = 0;

            foreach ($results as $show) {
                $id++;

                $table[] = array(
                    $id,
                    $show->season,
                    $show->episode,
                    $show->title,
                    $show->language,
                    $show->version,
                    $show->completed,
                    $show->hearingImpaired ? 'x' : '',
                    $show->hd ? 'x' : '',
                );

                $urls[$id] = $show->url;
            }

            if (empty($urls)) {
                $output->writeln('<error>[ERROR]</error> No Subtitle found.');
                continue;
            }

            $io->table(array(
                'Id',
                'Season',
                'Episode',
                'Title',
                'Language',
                'Version',
                'Competed',
                'HI',
                'HD',
            ), $table);

            $choice = $io->ask('Select an id (0 to skip)', $id, function ($number) use ($urls) {
                if (!is_numeric($number)) {
                    throw new \RuntimeException('You must type an integer.');
                }
                if ($number == 0) {
                    return 0;
                }
                if (!isset($urls[$number])) {
                    throw new \RuntimeException('You must type an id in the list.');
                }

                return $number;
            });

            if ($choice == 0) {
                $output->writeln('<info>[INFO]</info> Skipping.');
                continue;
            }

            $url = $urls[$choice];

            $output->writeln('<info>[INFO]</info> Downloading sub: '.$url);

            $request = $client->request('GET', $url);
            $subData = $request->getBody()->getContents();

            $output->writeln('<info>[INFO]</info> Saving to: '.$subFilename);

            file_put_contents($subFullpath, $subData);
        }
    }
}
