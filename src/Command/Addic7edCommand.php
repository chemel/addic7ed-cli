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
use Alc\Addic7edCli\Component\SubtitleSelector;
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
            ->addOption('erase', 'e', InputOption::VALUE_NONE, 'Erase existing subtitle.')
            ->addOption('proxy', 'p', InputOption::VALUE_OPTIONAL, 'Use proxy.')
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
        if ($input->getOption('proxy')) {
            $client->setProxy($input->getOption('proxy'));
        }
        $client = $client->getClient();

        $database = new Addic7edDatabase($client);

        $selector = new SubtitleSelector();

        $language = $input->getOption('lang');

        foreach ($finder as $file) {
            $output->writeln("\n".'<info>[INFO]</> Filename: '.$file->getFilename());

            $subBasename = $file->getBasename('.'.$file->getExtension());
            $subFilename = $subBasename.'.srt';
            $subFullpath = $file->getPath().'/'.$subFilename;

            if (!$input->getOption('erase') && file_exists($subFullpath)) {
                $output->writeln('<info>[INFO]</> Subtitle exist skipping.');
                continue;
            }

            $filenameParser = new FilenameParser($file->getFilename());

            $searchTerm = $filenameParser->getTitle();

            if (empty($searchTerm)) {
                $output->writeln('<error>[ERROR]</> Title not found.');
                $searchTerm = $io->ask('Enter title:', $subBasename);
            }

            $subtitles = $database->find($searchTerm, $language, $filenameParser->getSeason(), $filenameParser->getEpisode());

            if (empty($subtitles)) {
                $output->writeln('<error>[ERROR]</> No Subtitle found.');
                continue;
            }

            $table = array();

            foreach ($subtitles as $show) {
                $table[] = array(
                    '#'.$show->id,
                    $show->season,
                    $show->episode,
                    $show->title,
                    $show->language,
                    $show->version,
                    $show->completed,
                    $show->hearingImpaired ? 'x' : '',
                    $show->hd ? 'x' : '',
                );
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

            $id = $selector->suggest($subBasename, $subtitles);

            $choice = $io->ask('Select an id (0 to skip)', $id, function ($id) use ($subtitles) {
                if (!is_numeric($id)) {
                    throw new \RuntimeException('You must type an integer.');
                }
                if ($id == 0) {
                    return 0;
                }
                if (!isset($subtitles[$id])) {
                    throw new \RuntimeException('You must type an id in the list.');
                }

                return $id;
            });

            if ($choice == 0) {
                $output->writeln('<info>[INFO]</> Skipping.');
                continue;
            }

            $url = $subtitles[$choice]->url;

            $output->writeln('<info>[INFO]</> Downloading sub #'.$choice.': '.$url);

            $request = $client->request('GET', $url);
            $subData = $request->getBody()->getContents();

            $output->writeln('<info>[INFO]</> Saving to: '.$subFilename);

            file_put_contents($subFullpath, $subData);
        }
    }
}
