<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Service\Helper\PageUrlProvider;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ButtonMailFieldType implements MailFieldTypeInterface
{
    public function __construct(private PageUrlProvider $pageUrlProvider){

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
    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function build(array $item, string $locale): array
    {
        $item['url'] = $this->pageUrlProvider->getUrl($item, $locale);
        return $item;
    }
}
