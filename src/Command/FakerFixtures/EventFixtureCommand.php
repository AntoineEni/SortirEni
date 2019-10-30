<?php

namespace App\Command\FakerFixtures;

use App\Service\StateEnum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\User;

class EventFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:event';

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
        ->setDescription('Load fresh dummy data in event table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    public function Random(){
        return StateEnum::arrayEnumState()[array_rand(StateEnum::arrayEnumState())];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allLocationEntities = $this->doctrine->getRepository(Location::class)->findAll();
        $allSiteEntities = $this->doctrine->getRepository(Site::class)->findAll();
        $allUserEntities = $this->doctrine->getRepository(User::class)->findAll();

        for($i=0; $i<$num; $i++){
            $event = new Event();
            $date = $this->faker->dateTimeBetween($startDate = "- 1 months", $endDate = "+ 3 months");

            $event->setName($this->faker->name());
            $event->setDateDebut($date);
            $event->setDuration($this->faker->numberBetween($min = 1, $max = 3000));
            $event->setDateCloture($this->faker->dateTimeBetween($startDate = "- 3 months",$date));
            $event->setInscriptionsMax($this->faker->numberBetween($min = 0, $max = 100));
            $event->setDescription($this->faker->optional($chancesOfValue = 0.5)->text(50));
            $event->setPictureUrl($this->faker->optional($chancesOfValue = 0)->image());
            $event->setState($this->Random());

            $event->setLieu($this->faker->randomElement($allLocationEntities));
            $event->setSite($this->faker->randomElement($allSiteEntities));
            $event->setOrganisator($this->faker->randomElement($allUserEntities));

            $this->manager->persist($event);
        }

        $this->manager->flush();

        $io->writeln($num . ' "Event" loaded!');

        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE event");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}