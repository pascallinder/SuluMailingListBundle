<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

readonly class MailFieldTypeConfiguration
{
    /**
     * @param string[] $acceptedWrapper
     */
    public function __construct(private string $title, private string $xmlPath, private string $key,
                                private bool $onlyDoubleOpt = false, private array $acceptedWrapper = ['all']){

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

    public function isOnlyDoubleOpt(): bool
    {
        return $this->onlyDoubleOpt;
    }

    /**
     * @return string[]
     */
    public function getAcceptedWrapper(): array
    {
        return $this->acceptedWrapper;
    }
}