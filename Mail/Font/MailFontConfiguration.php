<?php

namespace Linderp\SuluMailingListBundle\Mail\Font;

readonly class MailFontConfiguration
{

    public function __construct(private string $cssUrl,
                                private string $name,
                                private string $fontFamily,
                                private bool $defaultFont = false)
    {
    }

    public function getCssUrl(): string
    {
        return $this->cssUrl;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    public function isDefaultFont(): bool
    {
        return $this->defaultFont;
    }
}