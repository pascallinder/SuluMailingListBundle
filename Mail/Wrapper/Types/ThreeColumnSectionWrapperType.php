<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper\Types;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;

readonly class ThreeColumnSectionWrapperType implements MailWrapperTypeInterface
{
    public function getConfiguration(): MailWrapperTypeConfiguration
    {
        return (new MailWrapperTypeConfiguration(
            'mailingListMail.props.content.threeColumnSection.label',
            __DIR__ . "/../../../Resources/config/mail/wrappers/three-column-section.xml",
            "three-column-section"
        ))->setContentKeys([
            'mailingListMail.props.content.columnOne' => 'columnOne',
            'mailingListMail.props.content.columnTwo' => 'columnTwo',
            'mailingListMail.props.content.columnThree' => 'columnThree',
        ])->setPriority(40);
    }

    public function build(array $wrapper, string $locale): array
    {
        return $wrapper;
    }
}