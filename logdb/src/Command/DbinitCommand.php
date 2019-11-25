<?php

namespace App\Command;

use App\Entity\AccessLog;
use App\Entity\Block;
use App\Entity\ExceptionLogs;
use App\Entity\HdfsLog;
use App\Entity\Logger;
use App\Utils\LogParser\Kottas\AccessLogParser;
use App\Utils\LogParser\Kottas\HdfsLogParserDataXReceiverLog;
use App\Utils\LogParser\Kottas\HdfsLogParserNamesystemLog;
use Cassandra\Date;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Kassner\LogParser\LogParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DbinitCommand extends Command
{
    protected static $defaultName = 'dbinit';
    private $container;

    protected function configure()
    {
        $this
            ->setDescription('Log DB initialization using external Logs')
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
        if ($pathTree[$totalPathDirectories - 1] == 'access.log') {
            $type = 1;
            $parser = new AccessLogParser();
        } elseif ($pathTree[$totalPathDirectories - 1] == 'HDFS_DataXceiver.log') {
            $type = 2;
            $parser = new HdfsLogParserDataXReceiverLog();
        } elseif ($pathTree[$totalPathDirectories - 1] == 'HDFS_FS_Namesystem.log') {
            $type = 3;
            $parser = new HdfsLogParserNamesystemLog();
        } else {
            $type = 4;
            $parser = new \App\Utils\LogParser\Kassner\LogParser\LogParser();
        }

        $em = $this->container->get("doctrine")->getManager();
        $counterToFlushWrites = 1;
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
                        $em->persist($badEntry);
                    } else {
                        $accessLog = new AccessLog();
                        $accessLog->setMethod($formattedLogs['formattedLog']['method']);
                        $accessLog->setReferer($formattedLogs['formattedLog']['referer']);
                        $accessLog->setRequestedResource($formattedLogs['formattedLog']['path']);
                        $accessLog->setResponseSize(intval($formattedLogs['formattedLog']['bytes']));
                        $accessLog->setResponseStatus($formattedLogs['formattedLog']['status']);
                        $accessLog->setUserAgent($formattedLogs['formattedLog']['agent']);

                        $log = new Logger();
                        $log->setDestIp($formattedLogs['formattedLog']['identity']);
                        $log->setSourceIp($formattedLogs['formattedLog']['ip']);
                        $log->setInsertDate($date);
                        $log->setAccessLog($accessLog);
                        $em->persist($log);
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown access log format");
                    $em->persist($badEntry);
                }
            } elseif ($type == 2) {
                $formattedLogs = $parser->format_hdfs_DataXReceiver_log_line($line);
                print_r($formattedLogs);
                if ($formattedLogs['badEntry'] == false) {
                    $date = \DateTime::createFromFormat("dmy His", $formattedLogs['formattedLog']['timeStamp']);
                    $size = 0;
                    if (empty($date)) {
                        $badEntry = new ExceptionLogs();
                        $badEntry->setInsertDate($now);
                        $badEntry->setLog($line);
                        $badEntry->setReason("bad date format");
                        $em->persist($badEntry);
                    } else {
                        $blockId = $em->getRepository("App\Entity\Block")->findOneBy(array("block_number" => $formattedLogs['formattedLog']['bid']));
                        if (empty($blockId)) {
                            $blockId = new Block();
                            $blockId->setBlockNumber($formattedLogs['formattedLog']['bid']);
                            $em->persist($blockId);
                        }

                        $hdfslog = new HdfsLog();
                        if ($formattedLogs['formattedLog']['size'] == "block") {
                            $size = 4;
                        }
                        $hdfslog->setSize($size);
                        $hdfslog->setType($formattedLogs['formattedLog']['type']);
                        $hdfslog->addBlock($blockId);

                        $log = new Logger();
                        $log->setDestIp($formattedLogs['formattedLog']['destinationIp']);
                        $log->setSourceIp($formattedLogs['formattedLog']['sourceIp']);
                        $log->setInsertDate($date);
                        $log->setHdfsLog($hdfslog);
                        $em->persist($log);
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown HDFS DataXReceiver log format");
                    $em->persist($badEntry);
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
                        $em->persist($badEntry);
                    } else {
                        $hdfslog = new HdfsLog();
                        $hdfslog->setSize($size);
                        $hdfslog->setType($formattedLogs['formattedLog']['type']);

                        foreach($formattedLogs['formattedLog']['blocks'] as $block) {
                            $blockId = $em->getRepository("App\Entity\Block")->findOneBy(array("block_number" => $block));
                            if (empty($blockId)) {
                                $blockId = new Block();
                                $blockId->setBlockNumber($block);
                                $em->persist($blockId);
                            }
                            $size = $size + 4;
                            $hdfslog->addBlock($blockId);
                        }

                        foreach($formattedLogs['formattedLog']['destinationIps'] as $destinationIp){
                            $log = new Logger();
                            $log->setDestIp($destinationIp);
                            $log->setSourceIp($formattedLogs['formattedLog']['sourceIp']);
                            $log->setInsertDate($date);
                            $log->setHdfsLog($hdfslog);
                            $em->persist($log);
                        }
                    }
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate($now);
                    $badEntry->setLog($line);
                    $badEntry->setReason("unknown HDFS Namesystem log format");
                    $em->persist($badEntry);
                }
            } else {
                //TODO general logs
            }
            //flush every 1000 entries to reduce I/O overhead and database constantly locking
            //TODO change ready to flush value to find best flush frequency
            if ($counterToFlushWrites % 1000 == 0) {
                print_r($counterToFlushWrites . "\n");
                $em->flush();
            } elseif($counterToFlushWrites % 100001 == 0){
                $em->clear();
                gc_enable();
                gc_collect_cycles();
            }
            $counterToFlushWrites++;
        }
        fclose($file);


        $io->success('Command completed Successfully');
    }
}
