<?php

namespace Linderp\SuluMailingListBundle\Event\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class NewsletterSubscribedEvent extends DomainEvent
{
    public function __construct(
        private readonly NewsletterSubscription $newsletterSubscription
    ) {
        parent::__construct();
    }

    public function getEventType(): string
    {
        return 'subscribed';
    }

    public function getResourceKey(): string
    {
        return NewsletterSubscription::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return $this->newsletterSubscription->getId();
    }
    public function getResourceTitle(): ?string
    {
        return $this->newsletterSubscription->getContact()->getFirstName() . " " .$this->newsletterSubscription->getContact()->getLastName()
            . ' ('. $this->newsletterSubscription->getContact()->getMainEmail(). ')'
            . ' - ' . $this->newsletterSubscription->getNewsletter()->getTitle($this->newsletterSubscription->getLocale());
    }

    /**
     * @return NewsletterSubscription
     */
    public function getNewsletterSubscription(): NewsletterSubscription
    {
        return $this->newsletterSubscription;
    }
}