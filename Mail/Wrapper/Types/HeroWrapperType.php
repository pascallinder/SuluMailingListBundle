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
        ))->setPriority(40);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function build(array $item, string $locale): array
    {
        $item['backgroundImage'] = $this->imageUrlProvider->getUrl($item, $locale);
        return $item;
    }
}
