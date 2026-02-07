<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfiguration;

class MailFieldTypeConfiguration extends MailMetadataXmlConfiguration
{
    /** @var string[] $acceptedResources */
    private array $acceptedResources = [];
    /**
     * @param string[] $acceptedContext;
     */
    private array $acceptedContext = [];
    /**
     * @param string[] $acceptedWrapper
     */
    private array $acceptedWrapper = [];

    public function setAcceptedResources(string ...$acceptedResources): static
    {
        $this->acceptedResources = $acceptedResources;
        return $this;
    }

    public function setAcceptedWrapper(string ...$acceptedWrapper): static
    {
        $this->acceptedWrapper = $acceptedWrapper;
        return $this;
    }
    public function getAcceptedResources(): array
    {
        return $this->acceptedResources;
    }
    /**
     * @return string[]
     */
    public function getAcceptedWrapper(): array
    {
        return $this->acceptedWrapper;
    }

    public function getAcceptedContext(): array
    {
        return $this->acceptedContext;
    }

    public function setAcceptedContext(string ...$acceptedContext): static
    {
        $this->acceptedContext = $acceptedContext;
        return $this;
    }

}