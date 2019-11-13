<?php

namespace App\Command;

use App\Entity\AccessLog;
use App\Entity\ExceptionLogs;
use App\Entity\Logger;
use App\Utils\LogParser\Kottas\AccessLogParser;
use App\Utils\LogParser\Kottas\HdfsLogParserDataXReceiverLog;
use App\Utils\LogParser\Kottas\HdfsLogParserNamesystemLog;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
            $io->note(sprintf('You passed an argument: %s', $arg1));
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
        } elseif ($pathTree[$totalPathDirectories - 1] = 'HDFS_DataXceiver.log') {
            $type = 2;
            $parser = new HdfsLogParserDataXReceiverLog();
        } elseif ($pathTree[$totalPathDirectories - 1] = 'HDFS_Namesystem.log') {
            $type = 3;
            $parser = new HdfsLogParserNamesystemLog();
        } else {
            $type = 4;
            $parser = new \App\Utils\LogParser\Kassner\LogParser\LogParser();
        }

        $em = $this->container->get("doctrine")->getManager();
        while (!feof($file)) {
            $line = fgets($file);
            if ($type == 1) {
                $formattedLogs = $parser->format_line($line);
                if ($formattedLogs['badEntry'] == false) {
                    $log = new Logger();
                    $log->setDestIp($formattedLogs['formattedLog']['identity']);
                    $log->setSourceIp($formattedLogs['formattedLog']['ip']);
                    print_r($line);
                    print_r("\n");
                    print_r($formattedLogs['formattedLog']['date'] . " " . $formattedLogs['formattedLog']['time']);
                    print_r("\n");
                    $date = \DateTime::createFromFormat("d/M/Y h:i:s", $formattedLogs['formattedLog']['date'] . " " . $formattedLogs['formattedLog']['time'] );
                    print_r($date);
                    print_r("\n");
                    $log->setTimeStamp(strtotime($date->format("Y-m-d h:i:s") ));

                    $accessLog = new AccessLog();
                    $accessLog->setMethod($formattedLogs['formattedLog']['method']);
                    $accessLog->setReferer($formattedLogs['formattedLog']['referer']);
                    $accessLog->setRequestedResource($formattedLogs['formattedLog']['path']);
                    $accessLog->setResponseSize($formattedLogs['formattedLog']['bytes']);
                    $accessLog->setResponseStatus($formattedLogs['formattedLog']['status']);
                    $accessLog->setUserAgent($formattedLogs['formattedLog']['agent']);

                    $log->setAccessLog($accessLog);
                    $em->getEntityManager()->persist($log);
                } else {
                    $badEntry = new ExceptionLogs();
                    $badEntry->setInsertDate(new \DateTime());
                    $badEntry->setLog($line);
                }
            } elseif ($type == 2) {

                $example = '081116 203518 143 INFO dfs.DataNode$DataXceiver: Receiving block blk_-1608999687919862906 src: /10.250.19.102:54106 dest: /10.250.19.102:50010';
                $formattedLogs = $parser->format_hdfs_DataXReceiver_log_line($line);
                print_r($formattedLogs);
                print_r("\n");
                exit();
            } elseif ($type == 3) {
                $formattedLogs = $parser->format_hdfs_Namesystem_log_line($line);
            } else {
                //TODO general logs
            }

        }
        fclose($file);


        $io->success('Command completed Successfully');
    }
}
