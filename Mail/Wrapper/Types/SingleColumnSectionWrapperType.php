<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper\Types;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;

readonly class SingleColumnSectionWrapperType implements MailWrapperTypeInterface
{
    public function getConfiguration(): MailWrapperTypeConfiguration
    {
        return (new MailWrapperTypeConfiguration(
            'mailingListMail.props.content.singleColumnSection.label',
            __DIR__ . "/../../../Resources/config/mail/wrappers/single-column-section.xml",
            "single-column-section"
        ))->setPriority(20)->setContentKeys([
            'mailingListMail.props.content.components' => 'columnOne'
        ]);
    }

    public function build(array $wrapper, string $locale): array
    {
        return $wrapper;
    }
}