<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Service\Helper\ImageUrlProvider;

readonly class ImageMailFieldType implements MailFieldTypeInterface
{
    public function __construct(private ImageUrlProvider $imageUrlProvider){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return new MailFieldTypeConfiguration(
            'mailingListMail.props.content.image.label',
            __DIR__ . "/../../../Resources/config/mail/types/image.xml",
            "image"
        );
    }

    public function build(array $item, string $locale): array
    {
        if(!array_key_exists('image',$item) || !$item['image']['id']){
            return [ ...$item, "image" => null];
        }
        return [ ...$item, "image" => $this->imageUrlProvider->getUrl($item['image']["id"], $locale)];
    }
}