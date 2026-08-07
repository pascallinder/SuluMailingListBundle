<?php

namespace Linderp\SuluMailingListBundle\Event\Newsletter;

use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class NewsletterUnsubscribedEvent extends DomainEvent
{
    public function __construct(
        private readonly NewsletterSubscription $newsletterSubscription
    ) {
        parent::__construct();
    }

    public function getEventType(): string
    {
        return 'unsubscribed';
    }

    public function getResourceKey(): string
    {
        return NewsletterSubscription::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->newsletterSubscription->getId();
    }

    public function getResourceTitle(): ?string
    {
        $contact = $this->newsletterSubscription->getContact();

        return trim(sprintf(
            '%s %s (%s) - %s',
            $contact->getFirstName() ?? '',
            $contact->getLastName() ?? '',
            $contact->getMainEmail(),
            $this->newsletterSubscription->getNewsletter()->getTitle($this->newsletterSubscription->getLocale()) ?? ''
        ));
    }
}
