<?php

namespace Linderp\SuluMailingListBundle\Mail\Resource;

readonly class MailResourceConfiguration
{
    public function __construct(private string $xmlPath, private bool $doubleOpt){

    }

    public function getXmlPath(): string
    {
        return $this->xmlPath;
    }

    public function isDoubleOpt(): bool
    {
        return $this->doubleOpt;
    }
}