<?php

namespace Linderp\SuluMailingListBundle\Mail\Font;

class MailFontConfiguration
{
    private bool $defaultFont = false;
    private bool $webFont = false;
    private float $strokeWidth = 0.0;
    public function __construct(
        private readonly string $cssUrl,
        private readonly string $name,
        private readonly string $fontFamily
    ) {}

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
    public function setDefaultFont(bool $defaultFont): static
    {
        $this->defaultFont = $defaultFont;
        return $this;
    }

    public function isWebFont(): bool
    {
        return $this->webFont;
    }

    public function setWebFont(bool $webFont): static
    {
        $this->webFont = $webFont;
        return $this;
    }

    public function getStrokeWidth(): float
    {
        return $this->strokeWidth;
    }

    public function setStrokeWidth(float $strokeWidth): static
    {
        $this->strokeWidth = max(0.0, $strokeWidth);

        return $this;
    }
}
