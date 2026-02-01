<?php

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class ImageUrlProvider
{
    public function __construct(private WebspaceManagerInterface $webspaceManager,
                                private MediaManagerInterface $mediaManager){

    }
    public function getUrl(string $mediaId, string $locale): string{
        $formats = $this->mediaManager->getFormatUrls([$mediaId], $locale);
        $url = $formats[$mediaId]['1080x.png'];
        return str_replace('/'.$locale,'',
            $this->webspaceManager->findUrlByResourceLocator($url, null,$locale));
    }
}