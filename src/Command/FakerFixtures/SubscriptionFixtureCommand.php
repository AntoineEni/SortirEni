<?php

namespace App\Command\FakerFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Subscription;
use App\Entity\Event;
use App\Entity\User;

class SubscriptionFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:subscription';

    protected $manager = null;
    protected $doctrine = null;
    protected $faker = null;

    public function __construct(RegistryInterface $doctrine, $name = null)
    {
        parent::__construct($name);
        $this->manager = $doctrine->getManager();
        $this->doctrine = $doctrine;
        $this->faker = \Faker\Factory::create($locale = 'fr_FR');
    }

    protected function configure()
    {
        $this
        ->setDescription('Load fresh dummy data in subscription table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allEventEntities = $this->doctrine->getRepository(Event::class)->findAll();
        $allUserEntities = $this->doctrine->getRepository(User::class)->findAll();

        for($i=0; $i<$num; $i++){
            $subscription = new Subscription();

            $subscription->setDateInscription($this->faker->dateTimeBetween($startDate = "- 3 months", $endDate = "now"));

            $subscription->setEvent($this->faker->randomElement($allEventEntities));
            $subscription->setParticipant($this->faker->randomElement($allUserEntities));

            $this->manager->persist($subscription);
        }

        $this->manager->flush();

        $io->writeln($num . ' "Subscription" loaded!');

        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE subscription");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}