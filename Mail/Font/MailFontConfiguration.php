<?php

namespace Linderp\SuluMailingListBundle\Mail\Font;

class MailFontConfiguration
{
    private bool $defaultFont = false;
    public function __construct(private readonly string $cssUrl,
                                private readonly string $name,
                                private readonly string $fontFamily)
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
    public function setDefaultFont(bool $defaultFont): static{
        $this->defaultFont = $defaultFont;
        return $this;
    }
}