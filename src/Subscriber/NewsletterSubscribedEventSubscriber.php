<?php

namespace Linderp\SuluMailingListBundle\Subscriber;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterSubscribedEvent;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionMailService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

readonly class NewsletterSubscribedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private SubscriptionMailService $subscriptionMailService){

    }
    public static function getSubscribedEvents(): array
    {
        return [
            NewsletterSubscribedEvent::class => "onNewsletterSubscribed"
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     */
    public function onNewsletterSubscribed(NewsletterSubscribedEvent $event){
        $subscription = $event->getNewsletterSubscription();
        $this->subscriptionMailService->sendDoubleOptMailToSubscriber($subscription);
    }
}