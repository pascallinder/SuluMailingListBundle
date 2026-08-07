<?php

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Content\Domain\Model\DimensionContentInterface;
use Sulu\Content\Infrastructure\Doctrine\DimensionContentQueryEnhancer;
use Sulu\Page\Domain\Repository\PageRepositoryInterface;

readonly class PageUrlProvider
{
    public function __construct(
        private WebspaceManagerInterface $webspaceManager,
        private PageRepositoryInterface $pageRepository,
    ) {}

    /**
     * @param array<string, mixed> $item
     */
    public function getUrl(array $item, string $locale): ?string
    {
        if (!array_key_exists('url', $item)) {
            return  null;
        }
        if (($item['url']['provider'] ?? null) === 'page') {
            $page = $this->pageRepository->findOneBy(
                ['uuid' => (string) $item['url']['href']],
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
                return null;
            }
            $dimensionContent = $page->getDimensionContents()->filter(
                static fn($content): bool => $content->getLocale() === $locale,
            )->first();
            $resourceSegment = $dimensionContent?->getRoute()?->getSlug();
            if (!is_string($resourceSegment)) {
                return null;
            }

            return $this->webspaceManager->findUrlByResourceLocator(
                $resourceSegment,
                null,
                $locale,
                $page->getWebspaceKey(),
            );
        } else {
            return $item['url']['href'] ?? null;
        }

    }
}
