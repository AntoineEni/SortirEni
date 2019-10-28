<?php

namespace App\Command;

use App\Entity\User;
use App\Service\StateEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEventStatusCommand extends Command
{
    protected static $defaultName = 'app:update-event-status';
    private $em;

    public function __construct(EntityManagerInterface $em, string $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Update the status of all event if conditions has been reached')
            ->setHelp("Just run the command with no option to update the events")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->progressStart(4);

        try {
            //Open to close if closure date has been reached
            $this->em->createQueryBuilder()
                ->update("App\\Entity\\Event", "e")
                ->set("e.state", ":state")
                ->where("e.state = :previousState")
                ->andWhere("e.dateCloture <= :currentDate")
                ->setParameter("state", StateEnum::STATE_CLOSE)
                ->setParameter("previousState", StateEnum::STATE_OPEN)
                ->setParameter("currentDate", new DateTime())
                ->getQuery()->execute();

            $io->progressAdvance();

            //Close to running if event start date has been reached
            $this->em->createQueryBuilder()
                ->update("App\\Entity\\Event", "e")
                ->set("e.state", ":state")
                ->where("e.state = :previousState")
                ->andWhere("e.dateDebut <= :currentDate")
                ->setParameter("state", StateEnum::STATE_RUNNING)
                ->setParameter("previousState", StateEnum::STATE_CLOSE)
                ->setParameter("currentDate", new DateTime())
                ->getQuery()->execute();

            $io->progressAdvance();

            //Running to finished if event start date + duration has been reached
            $this->em->createQueryBuilder()
                ->update("App\\Entity\\Event", "e")
                ->set("e.state", ":state")
                ->where("e.state = :previousState")
                ->andWhere("DATE_ADD(e.dateDebut, e.duration, 'minute') <= :currentDate")
                ->setParameter("state", StateEnum::STATE_FINISHED)
                ->setParameter("previousState", StateEnum::STATE_RUNNING)
                ->setParameter("currentDate", new DateTime())
                ->getQuery()->execute();

            $io->progressAdvance();

            //Finished/close to archived if start date + 1 month has been reached
            $this->em->createQueryBuilder()
                ->update("App\\Entity\\Event", "e")
                ->set("e.state", ":state")
                ->where("e.state = :previousState")
                ->orWhere("e.state = :previousState2")
                ->andWhere("DATE_ADD(e.dateDebut, 1, 'month') <= :currentDate")
                ->setParameter("state", StateEnum::STATE_ARCHIVED)
                ->setParameter("previousState", StateEnum::STATE_FINISHED)
                ->setParameter("previousState2", StateEnum::STATE_CANCELED)
                ->setParameter("currentDate", new DateTime())
                ->getQuery()->execute();

            $io->progressFinish();
        } catch (Exception $e) {
            $io->error("An error has been throw : " . $e->getMessage());
        } finally {
            $io->text("End of the command");
        }
    }
}
