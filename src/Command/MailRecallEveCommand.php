<?php

namespace App\Command;

use App\Service\MailerService;
use App\Service\StateEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MailRecallEveCommand extends Command
{
    protected static $defaultName = 'app:send-mail-recall-eve';

    private $em;
    private $mailerService;

    public function __construct(EntityManagerInterface $em, MailerService $mailerService, string $name = null)
    {
        $this->em = $em;
        $this->mailerService = $mailerService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Send a mail to the subscribers the eve of the event')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tomorrow  = new DateTime("tomorrow");

        $io = new SymfonyStyle($input, $output);

        try {
            $allEvent = $this->em->createQueryBuilder()
                ->select("e")
                ->from("App\\Entity\\Event", "e")
                ->where("e.state = :state_open")
                ->orWhere("e.state = :state_close")
                ->andWhere("e.dateDebut > :tomorrow")
                ->andWhere("e.dateDebut < :two_days_later")
                ->setParameter("state_open", StateEnum::STATE_OPEN)
                ->setParameter("state_close", StateEnum::STATE_CLOSE)
                ->setParameter("tomorrow", new DateTime("tomorrow"))
                ->setParameter("two_days_later", (new DateTime())->modify("+2 days"))
                ->getQuery()->getResult();

            $io->progressStart(count($allEvent));

            foreach ($allEvent as $event) {
                $this->mailerService->sendRecallEve($event);
                $io->progressAdvance();
            }

            $io->progressFinish();
        } catch (Exception $e) {
            $io->error("An error has been throw : " . $e->getMessage());
        } finally {
            $io->text("End of the command");
        }

        return 0;
    }
}
