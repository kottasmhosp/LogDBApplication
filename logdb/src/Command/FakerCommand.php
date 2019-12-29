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

class FakerCommand extends Command
{
    protected static $defaultName = 'faker';
    private $container;

    protected function configure()
    {
        $this
            ->setDescription('Fake data for PHP');
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);



        $io->success('Command completed Successfully');
    }
}
