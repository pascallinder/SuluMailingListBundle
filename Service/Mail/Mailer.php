<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class Mailer
{
    public function __construct(private MailerInterface     $mailer,
                                private MailContentProvider $mailContentProvider){

    }

    /**
     * @throws InvalidArgumentException
     */
    public function prepareMail(MailTranslatable $newsletterMail, NewsletterSubscription $subscription,
                                array $additionalData = []): Email
    {
        return (new Email())
            ->from($newsletterMail->getSenderMail())
            ->to(new Address($subscription->getContact()->getMainEmail()))
            ->subject($newsletterMail->getSubject($subscription->getLocale()))
            ->html($this->mailContentProvider->getMailTranslatableMailContent($newsletterMail,$subscription->getLocale(),[
                'firstName' => $subscription->getContact()->getFirstName(),
                'lastName'=> $subscription->getContact()->getLastName(),
                ...$additionalData
            ]));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMails(Email... $emails): void
    {
        foreach ($emails as $email){
            $this->mailer->send($email);
        }
    }
}