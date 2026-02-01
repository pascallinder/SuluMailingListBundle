<?php

namespace Linderp\SuluMailingListBundle\Mail\Wrapper;

readonly class MailWrapperTypeConfiguration
{
    public function __construct(private string $title, private string $xmlPath, private string $key){

    }
    public function getXmlPath(): string
    {
        return $this->xmlPath;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}