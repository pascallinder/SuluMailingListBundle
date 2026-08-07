<?php

namespace Linderp\SuluMailingListBundle\Subscriber;

use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterSubscribedEvent;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionMailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class NewsletterSubscribedEventSubscriber implements EventSubscriberInterface
{
    /** @var array<int, \Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription> */
    private array $pendingSubscriptions = [];

    public function __construct(
        private SubscriptionMailService $subscriptionMailService,
        private LoggerInterface $logger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            NewsletterSubscribedEvent::class => "onNewsletterSubscribed",
            KernelEvents::TERMINATE => 'onTerminate',
        ];
    }

    public function onNewsletterSubscribed(NewsletterSubscribedEvent $event): void
    {
        $subscription = $event->getNewsletterSubscription();
        $this->pendingSubscriptions[spl_object_id($subscription)] = $subscription;
    }

    public function onTerminate(TerminateEvent $event): void
    {
        $subscriptions = $this->pendingSubscriptions;
        $this->pendingSubscriptions = [];

        foreach ($subscriptions as $subscription) {
            try {
                $this->subscriptionMailService->sendDoubleOptMailToSubscriber($subscription);
            } catch (\Throwable $exception) {
                $this->logger->error('Unable to send newsletter double-opt-in email after subscription.', [
                    'subscriptionId' => $subscription->getId(),
                    'exception' => $exception,
                ]);
            }
        }
    }
}
