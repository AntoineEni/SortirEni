<?php


namespace App\Service;


use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailerService
{
    const EMAIL_SORTIR = "noreply@sortir.com";

    private $em;
    private $translator;
    private $mailer;
    private $environnement;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, Swift_Mailer $mailer, \Twig\Environment $environment)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->environnement = $environment;
    }

    public function sendToAllAfterPublish(Event $event) {
        $userToNotify = $this->em->getRepository(User::class)->toNotifyAfterPublish();

        $message = (new Swift_Message($this->translator->trans("mail.publish.subject")))
            ->setFrom(self::EMAIL_SORTIR)
            ->setBody(
                $this->environnement->render(
                    'emails/publish.html.twig',
                    ['event' => $event]
                ),
                'text/html'
            )
        ;

        foreach ($userToNotify as $user) {
            try {
                $message->setTo($user->getMail());
                $this->mailer->send($message);
            } catch (Exception $e) { }
        }
    }

    public function sendAfterEdit(Event $event) {
        $userToNotify = $this->em->getRepository(User::class)->toNotifyAfterEdit();

        $message = (new Swift_Message($this->translator->trans("mail.edit.subject")))
            ->setFrom(self::EMAIL_SORTIR)
            ->setBody(
                $this->environnement->render(
                    'emails/edit.html.twig',
                    ['event' => $event]
                ),
                'text/html'
            )
        ;

        foreach ($userToNotify as $user) {
            try {
                $message->setTo($user->getMail());
                $this->mailer->send($message);
            } catch (Exception $e) { }
        }
    }

    public function sendAfterSubscription(Event $event, Subscription $subscription) {
        $message = (new Swift_Message($this->translator->trans("mail." . ($subscription->getId() == null ? "un" : "") . "subscription.subject")))
            ->setFrom(self::EMAIL_SORTIR)
            ->setTo($event->getOrganisator()->getMail())
            ->setBody(
                $this->environnement->render(
                    'emails/subscription.html.twig',
                    ['event' => $event,
                        'subscription' => $subscription]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function sendRecallEve(Event $event) {
        $message = (new Swift_Message($this->translator->trans("mail.recall.subject")))
            ->setFrom(self::EMAIL_SORTIR)
            ->setBody(
                $this->environnement->render(
                    'emails/recall.html.twig',
                    ['event' => $event]
                ),
                'text/html'
            )
        ;

        $subscriberToNotify = $event->getSubscriptions();

        foreach ($subscriberToNotify as $subscription) {
            $message->setTo($subscription->getParticipant()->getMail());
            $this->mailer->send($message);
        }
    }
}