<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Linderp\SuluMailingListBundle\Event\NewsletterMail\NewsletterMailSentEvent;
use Linderp\SuluMailingListBundle\Service\Mail\Mailer;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

readonly class SubscriptionMailService
{
    public function __construct(private Mailer                        $subscriptionMailer,
                                private NewsletterUrlProvider         $unsubscribeUrlProvider,
                                private DomainEventCollectorInterface $domainEventCollector){

    }

    /**
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     */
    public function sendMailToSubscribers(NewsletterMail $newsletterMail): void
    {
        if($newsletterMail->isSent()){
            return;
        }
        /** @var NewsletterSubscription[] $subscriptions */
        $subscriptions = $newsletterMail->getNewsletters()
            ->map(fn(Newsletter $newsletter)=> $newsletter->getNewsletterSubscriptions()->getValues())
            ->reduce(fn(array $carry, array $subscriptions)=> [...$carry,...$subscriptions],[]);
        if(count($newsletterMail->getContacts())){
            $subscriptions = array_filter($subscriptions,function(NewsletterSubscription $subscription) use ($newsletterMail) {
                return $newsletterMail->getContacts()->contains($subscription->getContact());
            });
        }
        $map = [];
        $subscriptions = array_filter($subscriptions,function(NewsletterSubscription $subscription) use (&$map){
           if(!array_key_exists($subscription->getContact()->getId(), $map)){
               $map[$subscription->getContact()->getId()]=true;
               return true;
           }
           return false;
        });
        $mails = [];
        foreach ($subscriptions as $subscription){
            if($subscription->isConfirmed() && !$subscription->isUnsubscribed()){
                $this->domainEventCollector->collect(new NewsletterMailSentEvent($newsletterMail, $subscription));
                $mails[] =$this->subscriptionMailer->prepareMail($newsletterMail, $subscription,
                [ 'unsubscribeUrl' => $this->unsubscribeUrlProvider->getUnsubscribeUrl($subscription)]);
            }
        }
        $this->subscriptionMailer->sendMails(...$mails);
        $newsletterMail->setSent(true);
    }

    /**
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     */
    public function sendDoubleOptMailToSubscriber(NewsletterSubscription $subscription): void
    {
        if($subscription->isConfirmed()) {
            return;
        }
        $mail = $this->subscriptionMailer->prepareMail($subscription->getNewsletter()->getNewsletterDoubleOpt(), $subscription,
            [ 'doubleOptUrl' => $this->unsubscribeUrlProvider->getDoubleOptUrl($subscription),
                'unsubscribeUrl' => $this->unsubscribeUrlProvider->getUnsubscribeUrl($subscription)]);
        $this->subscriptionMailer->sendMails($mail);
    }
}