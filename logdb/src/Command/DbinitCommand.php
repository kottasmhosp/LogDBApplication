<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbinitCommand extends Command
{
    protected static $defaultName = 'dbinit';

    protected function configure()
    {
        $this
            ->setDescription('Log DB initialization using external Logs')
            ->addArgument('filepath', InputArgument::REQUIRED, 'External Log File ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('filepath');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        $file = fopen($arg1,"r");

        if(empty($file)){
            print_r("File " . "'" . $file . "'" . " does not exists");
        }

        while(!feof($file)) {
            $line = fgets($file);
            # do same stuff with the $line
        }
        fclose($file);



        $io->success('Command completed Successfully');
    }
}
