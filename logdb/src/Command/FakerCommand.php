<?php

namespace App\Command;

use App\Document\Log;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Faker\Factory;
use Faker\Generator;
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
    /**
     * @var Generator
     */
    protected $faker;
    private $dm;

    protected function configure()
    {
        $this
            ->setDescription('Fake data for PHP');
    }

    public function __construct(ContainerInterface $container,DocumentManager $dm)
    {
        parent::__construct();
        $this->container = $container;
        //Load factory early
        $this->faker = Factory::create();
        $this->dm = $dm;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        print_r($this->faker->firstName() . "\n");
        print_r($this->faker->address . "\n");
        print_r($this->faker->userName . "\n");
        print_r($this->faker->password . "\n");
        print_r( "\n");

        print_r($this->faker->firstName() . "\n");
        print_r($this->faker->address . "\n");
        print_r($this->faker->userName . "\n");
        print_r($this->faker->password . "\n");

        $io->success('Command completed Successfully');
    }
}
