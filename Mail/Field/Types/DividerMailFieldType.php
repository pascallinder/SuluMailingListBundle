<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;

readonly class DividerMailFieldType implements MailFieldTypeInterface
{
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.divider.label',
            __DIR__ . "/../../../Resources/config/mail/types/divider.xml",
            "divider"
        ))->setPriority(40);
    }

    public function build(array $item, string $locale): array
    {
        return $item;
    }
}