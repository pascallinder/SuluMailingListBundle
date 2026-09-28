<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Resource\Types\MailTemplateFooterResource;

readonly class UnsubscribeLinkMailFieldType implements MailFieldTypeInterface
{
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.unsubscribeLink.label',
            __DIR__ . '/../../../Resources/config/mail/types/unsubscribe-link.xml',
            'unsubscribe-link'
        ))->setAcceptedResources(MailTemplateFooterResource::class)->setPriority(20);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function build(array $item, string $locale): array
    {
        return $item;
    }
}
