<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;

readonly class TextMailFieldType implements MailFieldTypeInterface
{
    public function __construct(){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.text.label',
            __DIR__ . "/../../../Resources/config/mail/types/text.xml",
            "text"
        ))->setPriority(10);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function build(array $item, string $locale): array
    {
        if(array_key_exists('align', $item)){
            unset($item['align']);
        }
        return $item;
    }
}
