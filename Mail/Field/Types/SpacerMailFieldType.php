<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;

readonly class SpacerMailFieldType implements MailFieldTypeInterface
{
    public function __construct(){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.spacer.label',
            __DIR__ . "/../../../Resources/config/mail/types/spacer.xml",
            "spacer"
        ))->setPriority(40);
    }

    public function build(array $item, string $locale): array
    {
        return $item;
    }
}