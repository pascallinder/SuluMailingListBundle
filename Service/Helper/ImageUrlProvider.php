<?php

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

readonly class ImageUrlProvider
{
    public function __construct(private WebspaceManagerInterface $webspaceManager,
                                private MediaManagerInterface $mediaManager){

    }
    /**
     * @param array<string, mixed> $item
     */
    public function getUrl(array $item, string $locale): ?string{
        $media = $item['image'] ?? $item['backgroundImage'] ?? null;
        if (!\is_array($media) || !array_key_exists('id', $media) || !$media['id']) {
            return null;
        }
        $formats = $this->mediaManager->getFormatUrls([$media['id']], $locale);
        $url = $formats[$media['id']]['1080x.png'];
        return str_replace('/'.$locale,'',
            $this->webspaceManager->findUrlByResourceLocator($url, null,$locale));
    }
}
