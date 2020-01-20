<?php

namespace App\Command;

use App\Document\Log;
use App\Document\User;
use App\Document\Vote;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->setDescription('Fake data for Admins')
            ->addArgument('suppressVoteRequirement', InputArgument::OPTIONAL, 'Pass vote limitation to 1/3 if true');
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
        $arg1 = $input->getArgument('suppressVoteRequirement');

        if ($arg1) {
            $io2->note(sprintf('You filepath is: %s', $arg1));
        }

        /**
         * Least required votes to be inserted
         */
        $leastVotes = intval(round($this->dm->createQueryBuilder(Log::class)
                ->count()->getQuery()->execute() / 3, 0));

        /**
         * Current Total Votes in DB
         */
        $currentVotesDb = intval(round($this->dm->createQueryBuilder(Vote::class)
                ->count()->getQuery()->execute(), 0));

        $leastVotes = $leastVotes - $currentVotesDb;

        print_r("We need " . $leastVotes . " more votes\n");

        $totalVotes = 1;
        $counterFlush = 1;

        /**
         *
         * If argument given true then
         * Command should be stopped by hand
         */
        while ($totalVotes <= $leastVotes && $arg1 == false) {

            /**
             * Get a sample of the 1/3 of total logs
             * and iterate through it
             */
            $totalSamples = rand(500, 1000);
            $logs = $this->dm->createAggregationBuilder(Log::class)
                ->hydrate(Log::class)
                ->sample($totalSamples)
                ->execute();

            /**
             * Create new Admin
             */
            $admin = new User();
            $admin->setUsername($this->faker->userName);
            $admin->setAddress($this->faker->address);
            $admin->setEmail($this->faker->email);
            $admin->setPassword($this->encoder->encodePassword($admin, $this->faker->password));
            $admin->setPhoneNumber($this->faker->phoneNumber);
            $admin->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
            $admin->setVotes($totalSamples);
            $this->dm->persist($admin);

            /**
             * Register votes for each randomly fetched log
             */
            /** @var Log $log */
            foreach ($logs as $log) {

                /**
                 * Search if there is vote for log
                 */
                $voteExistsForLog =  $this->dm->createQueryBuilder(Vote::class)
                    ->field("log_id")
                    ->equals($log->getId())
                    ->count()
                    ->getQuery()
                    ->execute();


                if (empty($voteExistsForLog)) {

                    $vote = new Vote();
                    $vote->setLogId($log->getId());
                    $vote->addAdmin($admin->getUsername());
                    $vote->setSourceIp($log->getSourceIp());
                    $this->dm->persist($vote);
                    $totalVotes++;
                } else {
                    $voteExistsForLog->addAdmin($admin->getUsername());
                }

            }

            if ($totalVotes >= 500000 * $counterFlush) {
                print("Current Total Votes are:" . $totalVotes . "\n");
                $this->dm->flush();
                $counterFlush++;
            }

        }

        $this->dm->flush();

        $io2->success('Command completed Successfully');
    }
}
