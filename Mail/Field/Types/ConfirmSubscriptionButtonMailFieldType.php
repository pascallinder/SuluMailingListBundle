<?php

namespace Linderp\SuluMailingListBundle\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Resource\Types\DoubleOptMailResource;

readonly class ConfirmSubscriptionButtonMailFieldType implements MailFieldTypeInterface
{
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return (new MailFieldTypeConfiguration(
            'mailingListMail.props.content.confirmSubscriptionButton.label',
            __DIR__ . "/../../../Resources/config/mail/types/confirm-subscription-button.xml",
            "confirm-subscription-button"
        ))->setAcceptedResources(DoubleOptMailResource::class)->setPriority(20);
    }

    public function build(array $item, string $locale): array
    {
        return $item;
    }
}