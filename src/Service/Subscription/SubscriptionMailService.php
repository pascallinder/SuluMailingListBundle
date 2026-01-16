<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Linderp\SuluMailingListBundle\Service\Mail\Mailer;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SubscriptionMailService
{
    public function __construct(private readonly Mailer $subscriptionMailer,
    private readonly NewsletterUrlProvider              $unsubscribeUrlProvider){

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
        $mails = [];
        foreach ($subscriptions as $subscription){
            if($subscription->isConfirmed() && !$subscription->isUnsubscribed()){
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