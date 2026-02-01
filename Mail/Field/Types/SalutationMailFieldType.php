<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Content\Select\SalutationPrefixSelect;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;

readonly class SalutationMailFieldType implements MailFieldTypeInterface
{
    public function __construct( private SalutationPrefixSelect $salutationPrefixSelect){

    }
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return new MailFieldTypeConfiguration(
            'mailingListMail.props.content.salutation.label',
            __DIR__ . "/../../../Resources/config/mail/types/salutation.xml",
            "salutation"
        );
    }

    public function build(array $item, string $locale): array
    {
        return [ ...$item,
            "prefix"=> $this->salutationPrefixSelect->getValue($item['prefix'],$locale)];
    }
}