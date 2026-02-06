<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ButtonMailFieldType implements MailFieldTypeInterface
{
    public function __construct(private WebspaceManagerInterface $webspaceManager,
                                 #[Autowire('@sulu_document_manager.document_manager')]
                                 private DocumentManagerInterface $documentManager){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.button.label',
            __DIR__ . "/../../../Resources/config/mail/types/button.xml",
            "button"
        ))->setPriority(20);
    }

    /**
     * @throws DocumentManagerException
     */
    public function build(array $item, string $locale): array
    {
        if (!array_key_exists('url', $item)) {
            return [ ...$item, 'url' => null];
        }
        if($item['url']['provider'] === 'page'){
            $uuid = $item['url']['href'];
            /** @var PageDocument $document */
            $document = $this->documentManager->find($uuid, $locale);
            $url = $this->webspaceManager->findUrlByResourceLocator($document->getResourceSegment(), null,$locale);
            return [ ...$item, 'url' => $url];
        }
        else{
            return [ ...$item, 'url' => $item['url']['href']];
        }
    }
}