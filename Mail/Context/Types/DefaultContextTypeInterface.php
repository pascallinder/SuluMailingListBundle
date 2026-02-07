<?php
namespace Linderp\SuluMailingListBundle\Mail\Context\Types;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypeConfiguration;

class DefaultContextTypeInterface implements MailContextTypeInterface
{
    public function getConfiguration(): MailContextTypeConfiguration
    {
        return new MailContextTypeConfiguration(
            'mailingListMail.props.contexts.default.label',
            __DIR__ . "/../../../Resources/config/mail/contexts/default.xml",
            "default"
        );
    }

    public function build(array $item, string $locale): array
    {
        return $item;
    }
}