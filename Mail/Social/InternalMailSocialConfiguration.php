<?php

namespace Linderp\SuluMailingListBundle\Mail\Social;

readonly class InternalMailSocialConfiguration
{
    public function __construct(private string $name,
                                private string $title,
                                private ?string $src,
                                private bool $internal = true)
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }
}