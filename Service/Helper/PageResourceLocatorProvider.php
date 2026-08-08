<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Content\Domain\Model\DimensionContentInterface;
use Sulu\Content\Infrastructure\Doctrine\DimensionContentQueryEnhancer;
use Sulu\Page\Domain\Model\PageDimensionContentInterface;
use Sulu\Page\Domain\Repository\PageRepositoryInterface;

final readonly class PageResourceLocatorProvider
{
    public function __construct(private PageRepositoryInterface $pageRepository) {}

    /** @return array{resourceSegment: string, webspaceKey: string}|null */
    public function get(string $documentUuid, string $locale): ?array
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
        if (null === $page) {
            return null;
        }

        $dimensionContent = $page->getDimensionContents()->filter(
            static fn($content): bool => $content->getLocale() === $locale,
        )->first();
        if (!$dimensionContent instanceof PageDimensionContentInterface) {
            return null;
        }

        $resourceSegment = $dimensionContent->getRoute()?->getSlug();
        if (!\is_string($resourceSegment)) {
            return null;
        }

        return [
            'resourceSegment' => $resourceSegment,
            'webspaceKey' => $page->getWebspaceKey(),
        ];
    }
}
