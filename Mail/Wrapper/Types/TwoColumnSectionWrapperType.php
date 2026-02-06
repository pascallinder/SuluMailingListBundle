<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper\Types;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;

readonly class TwoColumnSectionWrapperType implements MailWrapperTypeInterface
{
    public function getConfiguration(): MailWrapperTypeConfiguration
    {
        return (new MailWrapperTypeConfiguration(
            'mailingListMail.props.content.twoColumnSection.label',
            __DIR__ . "/../../../Resources/config/mail/wrappers/two-column-section.xml",
            "two-column-section"
        ))->setContentKeys([
            'mailingListMail.props.content.columnOne' => 'columnOne',
            'mailingListMail.props.content.columnTwo' => 'columnTwo',
        ])->setPriority(30);
    }

    public function build(array $wrapper, string $locale): array
    {
        return $wrapper;
    }
}