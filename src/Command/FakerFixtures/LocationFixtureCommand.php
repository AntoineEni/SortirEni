<?php

namespace App\Command\FakerFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Location;
use App\Entity\City;

class LocationFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:location';

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
        ->setDescription('Load fresh dummy data in location table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allCityEntities = $this->doctrine->getRepository(City::class)->findAll();

        for($i=0; $i<$num; $i++){
            $location = new Location();

            $location->setName($this->faker->company);
            $location->setStreet($this->faker->optional($chancesOfValue = 0.5)->streetAddress(50));
            $location->setLatitude($this->faker->optional($chancesOfValue = 0.5)->latitude);
            $location->setLongitude($this->faker->optional($chancesOfValue = 0.5)->longitude);

            $location->setCity($this->faker->randomElement($allCityEntities));

            $this->manager->persist($location);
        }

        $this->manager->flush();

        $io->writeln($num . ' "Location" loaded!');

        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE location");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}