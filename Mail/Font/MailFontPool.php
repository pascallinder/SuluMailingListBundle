<?php

namespace Linderp\SuluMailingListBundle\Mail\Font;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsAlias(id: 'mailing.font-pool', public: true)]
class MailFontPool implements MailPoolInterface
{
    /** @var MailFontInterface[] */
    private array $fonts = [];
    private string $defaultFont;
    public function __construct(
        #[AutowireIterator('mailing.font')]
        iterable $fonts
    ) {
        /** @var MailFontInterface $font */
        foreach ($fonts as $font) {
            if($font->getConfiguration()->isDefaultFont()){
                $this->defaultFont = $font->getConfiguration()->getFontFamily();
            }
            $this->fonts[$font->getConfiguration()->getFontFamily()] = $font;
        }
    }
    public function getAll(): array{
        return $this->fonts;
    }

    public function getFontSelection(): array{
        return array_reduce($this->fonts, fn(array $carry, MailFontInterface $font)=> [...$carry,[
            'name'=> $font->getConfiguration()->getFontFamily(),
            'title'=>$font->getConfiguration()->getName()]], []);
    }
    public function getDefaultValue(): string{
        return $this->defaultFont;
    }
}