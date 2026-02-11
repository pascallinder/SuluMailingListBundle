<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Social\MailSocialPool;
use Linderp\SuluMailingListBundle\Service\Helper\PageUrlProvider;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;

readonly class SocialMailFieldType implements MailFieldTypeInterface
{
    public function __construct(private PageUrlProvider $pageUrlProvider,
    private MailSocialPool $mailSocialPool){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.social.label',
            __DIR__ . "/../../../Resources/config/mail/types/social.xml",
            "social"
        ))->setPriority(50);
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
        $elements = [];
        foreach ($item['elements'] as $element) {
            $element['url'] = $this->pageUrlProvider->getUrl($element, $locale);
            $mailSocial = $this->mailSocialPool->getOne($element['name']);
            if($mailSocial->getConfiguration()->getSrc() != null){
                $element['src'] = $mailSocial->getConfiguration()->getSrc();
            }
            $elements[] = $element;
        }
        $item['elements'] = $elements;
        return $item;
    }
}
