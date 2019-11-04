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

    //HDFS.log Formatter

    private function format_hdfs_DataXReceiver_log_line($line,$type){
        //081116 203518 143 INFO dfs.DataNode$DataXceiver: Receiving block blk_-1608999687919862906 src: /10.250.19.102:54106 dest: /10.250.19.102:50010
        //081118 005410 17129 WARN dfs.DataNode$DataXceiver: 10.250.15.67:50010:Got exception while serving blk_702432797797823248 to /10.251.201.204:
        //081116 203523 148 INFO dfs.DataNode$DataXceiver: 10.250.11.100:50010 Served block blk_-3544583377289625738 to /10.250.19.102
        $explodedLine = explode(" ",$line);
        if ($explodedLine[3] == 'INFO') {
            //Destination Ip is before Source Ip otherwise destination ip is before block
            if($explodedLine[10] == 'dest:') {
                $formatted_line = array(
                    "TimeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                    "BlockId" => $explodedLine[7],
                    "SourceIp" => $explodedLine[10],
                    "DestinationIp" => $explodedLine[11],
                    "Size" => $explodedLine[6],
                    "Type" => $explodedLine[5]
                );
            } else {
                $formatted_line = array(
                    "TimeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                    "BlockId" => $explodedLine[8],
                    "SourceIp" => $explodedLine[10],
                    "DestinationIp" => $explodedLine[11],
                    "Size" => $explodedLine[7],
                    "Type" => $explodedLine[6]
                );
            }
        } elseif ($explodedLine[3] == 'WARN') {
            //Destination Ip is before type Ip
            $exceptionLineExploded = explode(":",$explodedLine[5]);
            $formatted_line = array(
                "TimeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                "BlockId" => $explodedLine[10],
                "SourceIp" => $exceptionLineExploded[0],
                "DestinationIp" => $explodedLine[12],
                "Size" => $explodedLine[9],
                "Type" => $explodedLine[8]
            );
        }
        return $formatted_line;
    }

    private function format_hdfs_DataXReceiver_line($line,$type){


        $HDFSlogs = $this->format_log_line($line); // format the line

        if (isset($HDFSlogs[0])) // check that it formated OK
        {
            $formated_log = array(); // make an array to store the lin info in
            $formated_log['ip'] = $HDFSlogs[1];
            $formated_log['identity'] = $HDFSlogs[2];
            $formated_log['user'] = $HDFSlogs[2];
            $formated_log['date'] = $HDFSlogs[4];
            $formated_log['time'] = $HDFSlogs[5];
            $formated_log['timezone'] = $HDFSlogs[6];
            $formated_log['method'] = $HDFSlogs[7];
            $formated_log['path'] = $HDFSlogs[8];
            $formated_log['protocal'] = $HDFSlogs[9];
            $formated_log['status'] = $HDFSlogs[10];
            $formated_log['bytes'] = $HDFSlogs[11];
            $formated_log['referer'] = $HDFSlogs[12];
            $formated_log['agent'] = $HDFSlogs[13];
            return $formated_log; // return the array of info
        }
        else
        {
            $this->badRows++; // if the row is not in the right format add it to the bad rows
            return false;
        }
    }

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
