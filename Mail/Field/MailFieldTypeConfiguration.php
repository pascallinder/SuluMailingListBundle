<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfiguration;

class MailFieldTypeConfiguration extends MailMetadataXmlConfiguration
{
    /** @var list<class-string> $acceptedResources */
    private array $acceptedResources = [];
    /**
     * @var list<string>
     */
    private array $acceptedContext = [];
    /**
     * @var list<string>
     */
    private array $acceptedWrapper = [];

    /**
     * @param class-string ...$acceptedResources
     */
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
    /**
     * @return list<class-string>
     */
    public function getAcceptedResources(): array
    {
        return $this->acceptedResources;
    }
    /**
     * @return list<string>
     */
    public function getAcceptedWrapper(): array
    {
        return $this->acceptedWrapper;
    }

    /**
     * @return list<string>
     */
    public function getAcceptedContext(): array
    {
        return $this->acceptedContext;
    }

    /**
     * @param string ...$acceptedContext
     */
    public function setAcceptedContext(string ...$acceptedContext): static
    {
        $this->acceptedContext = $acceptedContext;
        return $this;
    }

}
