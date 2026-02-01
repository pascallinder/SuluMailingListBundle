<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper\Types;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;

readonly class MultiColumnSectionWrapperType implements MailWrapperTypeInterface
{
    public function getConfiguration(): MailWrapperTypeConfiguration
    {
        return new MailWrapperTypeConfiguration(
            'mailingListMail.props.content.multiColumnSection.label',
            __DIR__ . "/../../../Resources/config/mail/wrappers/multi-column-section.xml",
            "multi-column-section"
        );
    }

    public function build(array $wrapper, string $locale): array
    {
        return $wrapper;
    }
}