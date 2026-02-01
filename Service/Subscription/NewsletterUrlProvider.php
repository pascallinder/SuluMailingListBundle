<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class NewsletterUrlProvider
{
    public function __construct(private readonly WebspaceManagerInterface  $webspaceManager)
    {
    }
    public function getUnsubscribeUrl(NewsletterSubscription $subscription): string
    {
        $url = "/newsletter/unsubscribe/{$subscription->getNewsletter()->getId()}/{$subscription->getUnsubscribeToken()}";
        return $this->webspaceManager->findUrlByResourceLocator($url, null,$subscription->getLocale());
    }
    public function getDoubleOptUrl(NewsletterSubscription $subscription): string
    {
        $url = "/newsletter/confirm/{$subscription->getNewsletter()->getId()}/{$subscription->getConfirmationToken()}";
        return $this->webspaceManager->findUrlByResourceLocator($url, null,$subscription->getLocale());
    }
}