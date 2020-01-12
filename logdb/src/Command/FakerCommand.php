<?php

namespace App\Command;

use App\Document\Log;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
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
            ->setDescription('Fake data for Admins');
    }

    public function __construct(ContainerInterface $container, DocumentManager $dm, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->container = $container;
        //Load factory early
        $this->faker = Factory::create();
        $this->dm = $dm;
        $this->encoder = $encoder;
    }

    /**
     *
     * Description : Generate Admins but also,
     * Ensure that 1/3 of the logs have at least one upvote
     * ,and no administrator has more than 1000 upvotes.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws MongoDBException
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io2 = new SymfonyStyle($input, $output);

        $totalVotes = $this->dm->createQueryBuilder(Log::class)
                ->count() / 3;

        $currentVotes = 1;
        $currentAdmins = 0;
        while ($currentVotes < $totalVotes) {

            $logs = $this->dm->createAggregationBuilder(Log::class)->sample($totalVotes);
            foreach ($logs as $log) {
                $currentAdminVotes = 0;
                $currentAdmins++;
                echo "Insert Admin" . $currentAdmins;
                $admin = new User();
                $admin->setUsername($this->faker->userName);
                $admin->setAddress($this->faker->address);
                $admin->setEmail($this->faker->email);
                $admin->setPassword($this->encoder->encodePassword($admin, $this->faker->password));
                $admin->setPhone($this->faker->phoneNumber);
                $admin->setRoles('{ROLE_ADMIN}');
                $this->dm->persist($admin);
            }


        }
        $this->dm->flush();

        $io2->success('Command completed Successfully');
    }
}
