<?php

namespace App\Command;

use App\Document\Log;
use App\Document\ExceptionLogs;
use App\Utils\LogParser\Kottas\AccessLogParser;
use App\Utils\LogParser\Kottas\HdfsLogParserDataXReceiverLog;
use App\Utils\LogParser\Kottas\HdfsLogParserNamesystemLog;
use App\Utils\LogParser\Kassner\LogParser;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MongoDbinitCommand extends Command
{
    protected static $defaultName = 'mongodbinit';
    private $container;

    protected function configure()
    {
        $this
            ->setDescription('Log DB initialization using external Logs and MongoDB')
            ->addArgument('filepath', InputArgument::REQUIRED, 'External Log File ');
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('filepath');

        if ($arg1) {
            $io->note(sprintf('You filepath is: %s', $arg1));
        }

        $file = fopen($arg1, "r");

        if (empty($file)) {
            print_r("File " . "'" . $file . "'" . " does not exists");
        }

        $pathTree = explode("/", $arg1);
        $type = '';
        $totalPathDirectories = count($pathTree);
        $parser = '';
        if ($pathTree[$totalPathDirectories - 1] == 'access.log' || $pathTree[$totalPathDirectories - 1] == 'access_part2.log' || $pathTree[$totalPathDirectories - 1] == 'access_part3.log' || $pathTree[$totalPathDirectories - 1] == 'acces_last.log') {
            $type = 1;
            $parser = new AccessLogParser();
        } elseif ($pathTree[$totalPathDirectories - 1] == 'HDFS_DataXceiver.log' || $pathTree[$totalPathDirectories - 1] == 'HDFS_DataXceive_part2.log' || $pathTree[$totalPathDirectories - 1] == 'HDFS_DataXceiver_final.log' ) {
            $type = 2;
            $parser = new HdfsLogParserDataXReceiverLog();
        } elseif ($pathTree[$totalPathDirectories - 1] == 'HDFS_FS_Namesystem_part1.log' || $pathTree[$totalPathDirectories - 1] == 'HDFS_FS_Namesystem_part2.log') {
            $type = 3;
            $parser = new HdfsLogParserNamesystemLog();
        } else {
            $type = 4;
            $parser = new \App\Utils\LogParser\Kassner\LogParser\LogParser();
        }

        $counterToFlushWrites = 1;
        $dm = $this->container->get("doctrine_mongodb")->getManager();
        while (!feof($file)) {
            $line = fgets($file);
            $now = new \DateTime();
            if ($type == 1) {
                $formattedLogs = $parser->format_line($line);
                if ($formattedLogs['badEntry'] == false) {
                    $date = \DateTime::createFromFormat("d/M/Y H:i:s", $formattedLogs['formattedLog']['date'] . " " . $formattedLogs['formattedLog']['time']);
                    if (empty($date)) {
                        $badEntry = new ExceptionLogs();
                        $badEntry->setInsertDate($now);
                        $badEntry->setLog($line);
                        $badEntry->setReason("bad date format");
                        $dm->persist($badEntry);
                    } else {
                        $log = new Log();
                        $log->setMethod($formattedLogs['formattedLog']['method']);
                        $log->setReferer($formattedLogs['formattedLog']['referer']);
                        $log->setRequestedResource($formattedLogs['formattedLog']['path']);
                        $log->setResponseSize(intval($formattedLogs['formattedLog']['bytes']));
                        $log->setResponseStatus($formattedLogs['formattedLog']['status']);
                        $log->setUserAgent($formattedLogs['formattedLog']['agent']);
                        $log->setDestIp($formattedLogs['formattedLog']['identity']);
                        $log->setSourceIp($formattedLogs['formattedLog']['ip']);
                        $log->setInsertDate($date);
                        //Required by ODM when block number set as collection type
                        $log->setBlockNull();
                        $dm->persist($log);
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown access log format");
                    $dm->persist($badEntry);
                }
            } elseif ($type == 2) {
                $formattedLogs = $parser->format_hdfs_DataXReceiver_log_line($line);
                if ($formattedLogs['badEntry'] == false) {
                    $date = \DateTime::createFromFormat("dmy His", $formattedLogs['formattedLog']['timeStamp']);

                    if (empty($date)) {
                        $badEntry = new ExceptionLogs();
                        $badEntry->setInsertDate($now);
                        $badEntry->setLog($line);
                        $badEntry->setReason("bad date format");
                        $dm->persist($badEntry);
                    } else {
                        $log = new Log();
                        $log->setSize(4);
                        $log->setType($formattedLogs['formattedLog']['type']);
                        $log->addBlock($formattedLogs['formattedLog']['bid']);
                        $log->setDestIp($formattedLogs['formattedLog']['destinationIp']);
                        $log->setSourceIp($formattedLogs['formattedLog']['sourceIp']);
                        $log->setInsertDate($date);
                        $dm->persist($log);
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown HDFS DataXReceiver log format");
                    $dm->persist($badEntry);
                }
            } elseif ($type == 3) {
                $formattedLogs = $parser->format_hdfs_Namesystem_log_line($line);
                if ($formattedLogs['badEntry'] == false) {
                    $date = \DateTime::createFromFormat("dmy His", $formattedLogs['formattedLog']['timeStamp']);
                    $size = 0;
                    if (empty($date)) {
                        $badEntry = new ExceptionLogs();
                        $badEntry->setInsertDate($now);
                        $badEntry->setLog($line);
                        $badEntry->setReason("bad date format");
                        $dm->persist($badEntry);
                    } else {
                        $blk = array();

                        //Creat an array from blocks
                        foreach($formattedLogs['formattedLog']['blocks'] as $block) {
                            $cblk = intval(str_replace("blk_","",$block));
                            $size = $size + 4;
                            if (!in_array($cblk, $blk)) {
                                $blk[] = $cblk;
                            }
                        }

                        //Set log for each destinationIp
                        foreach($formattedLogs['formattedLog']['destinationIps'] as $destinationIp){
                            $log = new Log();
                            $log->setType($formattedLogs['formattedLog']['type']);
                            $log->setDestIp($destinationIp);
                            $log->setSourceIp($formattedLogs['formattedLog']['sourceIp']);
                            $log->setInsertDate($date);
                            $log->setBlock($blk);
                            $log->setSize($size);
                            $dm->persist($log);
                        }
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown HDFS Namesystem log format");
                    $dm->persist($badEntry);
                }
            } else {
                //TODO general logs
            }
            //flush every 1000 entries to reduce I/O overhead and database constantly locking
            //TODO change ready to flush value to find best flush frequency
            if ($counterToFlushWrites % 1000 == 0) {
                print_r($counterToFlushWrites . "\n");
                $dm->flush();
            } elseif($counterToFlushWrites % 100001 == 0){
                $dm->clear();
                gc_enable();
                gc_collect_cycles();
            }
            $counterToFlushWrites++;
        }
        fclose($file);


        $io->success('Command completed Successfully');
    }
}
