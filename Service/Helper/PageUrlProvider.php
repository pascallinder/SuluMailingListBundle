<?php

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

readonly class PageUrlProvider
{
    public function __construct(
        private WebspaceManagerInterface $webspaceManager,
        private PageResourceLocatorProvider $pageResourceLocatorProvider,
    ) {}

    /**
     * @param array<string, mixed> $item
     */
    public function getUrl(array $item, string $locale): ?string
    {
        $url = $item['url'] ?? null;
        if (!\is_array($url)) {
            return null;
        }

        $href = $url['href'] ?? null;
        if (!\is_string($href)) {
            return null;
        }

        if (($url['provider'] ?? null) === 'page') {
            $page = $this->pageResourceLocatorProvider->get($href, $locale);
            if (null === $page) {
                return null;
            }

            return $this->webspaceManager->findUrlByResourceLocator(
                $page['resourceSegment'],
                null,
                $locale,
                $page['webspaceKey'],
            );
        }

        return $href;
    }
}
