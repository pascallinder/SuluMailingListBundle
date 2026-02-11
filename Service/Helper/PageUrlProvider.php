<?php

namespace Linderp\SuluMailingListBundle\Service\Helper;

use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PageUrlProvider
{
    public function __construct(private WebspaceManagerInterface $webspaceManager,
                                #[Autowire('@sulu_document_manager.document_manager')]
                                private DocumentManagerInterface $documentManager){

    }

    /**
     * @throws DocumentManagerException
     */
    /**
     * @param array<string, mixed> $item
     */
    public function getUrl(array $item, string $locale): ?string{
        if (!array_key_exists('url', $item)) {
            return  null;
        }
        if($item['url']['provider'] === 'page'){
            $document = $this->documentManager->find($item['url']['href'], $locale);
            return $this->webspaceManager->findUrlByResourceLocator($document->getResourceSegment(), null,$locale);
        }
        else{
            return $item['url']['href'];
        }

    }
}
