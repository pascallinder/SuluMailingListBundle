<?php

namespace Linderp\SuluMailingListBundle\Mail;

abstract class MailMetadataXmlConfiguration
{
    private int $priority = 0;
    public function __construct(private readonly string $title, private readonly string $xmlPath,
                                private readonly string $key){

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
    public function getPriority(): int{
        return $this->priority;
    }
    public function setPriority(int $priority): static{
        $this->priority = $priority;
        return $this;
    }
}