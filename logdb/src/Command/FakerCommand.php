<?php

namespace App\Command;

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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakerCommand extends Command
{
    protected static $defaultName = 'faker';
    private $container;
    private $encoder;
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

    public function __construct(ContainerInterface $container,DocumentManager $dm,UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->container = $container;
        //Load factory early
        $this->faker = Factory::create();
        $this->dm = $dm;
        $this->encoder = $encoder;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        for($i=1;$i<=10000;$i++){
            echo "Insert Admin" . $i;
            $admin = new User();
            $admin->setUsername($this->faker->userName);
            $admin->setAddress($this->faker->address);
            $admin->setEmail($this->faker->email);
            $admin->setPassword($this->encoder->encodePassword($admin, $this->faker->password));
            $admin->setRoles('{ROLE_ADMIN}');
            $this->dm->persist($admin);
        }
        $this->dm->flush();

        $io->success('Command completed Successfully');
    }
}
