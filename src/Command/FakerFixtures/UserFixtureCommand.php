<?php

namespace App\Command\FakerFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;
use App\Entity\Site;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:user';

    protected $manager = null;
    protected $doctrine = null;
    protected $faker = null;
    protected $encrypt;

    public function __construct(RegistryInterface $doctrine, $name = null,UserPasswordEncoderInterface $encoder)
    {
        parent::__construct($name);
        $this->manager = $doctrine->getManager();
        $this->doctrine = $doctrine;
        $this->faker = \Faker\Factory::create($locale = 'fr_FR');
        $this->encrypt=$encoder;
    }

    protected function configure()
    {
        $this
        ->setDescription('Load fresh dummy data in user table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allSiteEntities = $this->doctrine->getRepository(Site::class)->findAll();
        $user = new User();

        $user->setUsername('admin');
        $user->setPassword($this->encrypt->encodePassword($user, 'admin'));
        $user->setName('admin');
        $user->setFirstName('admin');
        $user->setPhone($this->faker->optional($chancesOfValue = 0.5)->phoneNumber);
        $user->setMail($this->faker->unique()->email);
        $user->setIsAdmin(1);
        $user->setIsActif(1);

        $user->setSite($this->faker->randomElement($allSiteEntities));

        $this->manager->persist($user);

        for($i=0; $i<$num; $i++){
            $user = new User();

            $user->setUsername($this->faker->unique()->username);
            $user->setPassword($this->encrypt->encodePassword($user, 'test'));
            $user->setName($this->faker->lastName);
            $user->setFirstName($this->faker->firstName);
            $user->setPhone($this->faker->optional($chancesOfValue = 0.5)->phoneNumber);
            $user->setMail($this->faker->unique()->email);
            $user->setIsAdmin($this->faker->boolean($chanceOfGettingTrue = 50));
            $user->setIsActif($this->faker->boolean($chanceOfGettingTrue = 50));

            $user->setSite($this->faker->randomElement($allSiteEntities));

            $this->manager->persist($user);
        }

        $this->manager->flush();

        $io->writeln($num . ' "User" loaded!');

        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE user");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}