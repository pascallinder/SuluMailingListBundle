<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper\Types;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;
use Linderp\SuluMailingListBundle\Service\Helper\ImageUrlProvider;

readonly class HeroWrapperType implements MailWrapperTypeInterface
{
    public function __construct(private ImageUrlProvider $imageUrlProvider){
    }
    public function getConfiguration(): MailWrapperTypeConfiguration
    {
        return (new MailWrapperTypeConfiguration(
            'mailingListMail.props.content.hero.label',
            __DIR__ . "/../../../Resources/config/mail/wrappers/hero.xml",
            "hero"
        ))->setPriority(10);
    }

    public function build(array $wrapper, string $locale): array
    {
        if(!array_key_exists('backgroundImage',$wrapper) || !$wrapper['backgroundImage']['id']){
            return [ ...$wrapper, "backgroundImage" => null];
        }
        return [ ...$wrapper, "backgroundImage" => $this->imageUrlProvider->getUrl($wrapper['backgroundImage']["id"], $locale)];
    }
}