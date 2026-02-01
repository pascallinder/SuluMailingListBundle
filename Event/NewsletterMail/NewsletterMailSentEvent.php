<?php

namespace Linderp\SuluMailingListBundle\Event\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class NewsletterMailSentEvent extends DomainEvent
{
    public function __construct(
        private readonly NewsletterMail $newsletterMail,
        private readonly NewsletterSubscription $newsletterSubscription,
    ) {
        parent::__construct();
    }

    public function getEventType(): string
    {
        return 'sent';
    }

    public function getResourceKey(): string
    {
        return NewsletterMail::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return $this->newsletterMail->getId();
    }
    public function getResourceTitle(): ?string
    {
        return $this->newsletterMail->getSubject() . ' -> ' . $this->newsletterSubscription->getContact()->getMainEmail();
    }

    /**
     * @return NewsletterSubscription
     */
    public function getNewsletterSubscription(): NewsletterSubscription
    {
        return $this->newsletterSubscription;
    }

    public function getNewsletterMail(): NewsletterMail
    {
        return $this->newsletterMail;
    }
}