<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;

use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Sulu\Content\Domain\Model\DimensionContentInterface;
use Sulu\Content\Infrastructure\Doctrine\DimensionContentQueryEnhancer;
use Sulu\Page\Domain\Repository\PageRepositoryInterface;

readonly class SubscriptionDocumentUrlProvider
{
    public function __construct(
        private NewsletterRepository $newsletterRepository,
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function getUnsubscribePageUrl(string $newsletterId, string $locale): string
    {
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter->getUnsubscribePage(), $locale);
    }

    public function getConfirmedDoubleOptPageUrl(string $newsletterId, string $locale): string
    {
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter->getDoubleOptConfirmPage(), $locale);
    }

    private function getUrl(string $documentUuid, string $locale): string
    {
        $page = $this->pageRepository->findOneBy(
            ['uuid' => $documentUuid],
            [
                PageRepositoryInterface::SELECT_PAGE_CONTENT => [
                    'dimensionAttributes' => [
                        'locale' => $locale,
                        'stage' => DimensionContentInterface::STAGE_LIVE,
                        'version' => DimensionContentInterface::CURRENT_VERSION,
                    ],
                    'selects' => [DimensionContentQueryEnhancer::GROUP_SELECT_CONTENT_WEBSITE => true],
                ],
            ],
        );
        if (!$page) {
            return '/' . $locale;
        }
        $dimensionContent = $page->getDimensionContents()->filter(
            static fn($content): bool => $content->getLocale() === $locale,
        )->first();
        $resourceSegment = $dimensionContent?->getRoute()?->getSlug() ?? '/';

        return '/' . $locale . $resourceSegment;
    }
}
