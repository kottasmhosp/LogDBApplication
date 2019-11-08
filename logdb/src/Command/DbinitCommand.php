<?php

namespace App\Command;

use Kassner\LogParser\LogParser;
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

        $pathTree = explode("/",$arg1);
        //print_r($pathTree);
        $type = '';
        $totalPathDirectories = count($pathTree);
        if($pathTree[$totalPathDirectories - 1] == 'access.log'){
            $type = 1;
        } elseif ($pathTree[$totalPathDirectories - 1] = 'HDFS_DataXceiver.log') {
            $type = 2;

        } else {
            $type = 3;
        }

        while(!feof($file)) {
            $line = fgets($file);
            if($type == 1) {
                $formattedLogs = $this->format_line($line);
                print_r($formattedLogs);
                print_r("\n");
                exit();
            } elseif($type == 2){
                $example = '081116 203518 143 INFO dfs.DataNode$DataXceiver: Receiving block blk_-1608999687919862906 src: /10.250.19.102:54106 dest: /10.250.19.102:50010';
                $formattedLogs = $this->format_hdfs_DataXReceiver_line($line);
                print_r($formattedLogs);
                print_r("\n");
                exit();
            } else {
                //Other HDFS logs
            }

        }
        fclose($file);



        $io->success('Command completed Successfully');
    }
}
