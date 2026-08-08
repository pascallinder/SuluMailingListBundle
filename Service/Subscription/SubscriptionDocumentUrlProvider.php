<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;

use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Service\Helper\PageResourceLocatorProvider;

readonly class SubscriptionDocumentUrlProvider
{
    public function __construct(
        private NewsletterRepository $newsletterRepository,
        private PageResourceLocatorProvider $pageResourceLocatorProvider,
    ) {}

    public function getUnsubscribePageUrl(string $newsletterId, string $locale): string
    {
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter?->getUnsubscribePage(), $locale);
    }

    public function getConfirmedDoubleOptPageUrl(string $newsletterId, string $locale): string
    {
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter?->getDoubleOptConfirmPage(), $locale);
    }

    private function getUrl(?string $documentUuid, string $locale): string
    {
        if (null === $documentUuid) {
            return '/' . $locale;
        }

        $page = $this->pageResourceLocatorProvider->get($documentUuid, $locale);
        if (null === $page) {
            return '/' . $locale;
        }

        return '/' . $locale . ($page['resourceSegment'] ?: '/');
    }
}
