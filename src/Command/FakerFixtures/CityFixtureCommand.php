<?php

namespace App\Command\FakerFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\City;

class CityFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:city';

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
        ->setDescription('Load fresh dummy data in city table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();


        for($i=0; $i<$num; $i++){
            $city = new City();

            $city->setName($this->faker->city);
            $city->setPostalCode($this->faker->postcode);


            $this->manager->persist($city);
        }

        $this->manager->flush();

        $io->writeln($num . ' "City" loaded!');

        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE city");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}