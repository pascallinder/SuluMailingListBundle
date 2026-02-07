<?php

namespace Linderp\SuluMailingListBundle\Mail\Wrapper;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfiguration;

class MailWrapperTypeConfiguration extends MailMetadataXmlConfiguration
{
    /**
     * @var array<string,string>
     */
    private array $contentKeys = ['mailingListMail.props.content.components'=>'components'];

    /**
     * @return array<string,string>
     */
    public function getContentKeys(): array{
        return $this->contentKeys;
    }

    /**
     * @param array<string, string> $contentKeys
     */
    public function setContentKeys(array $contentKeys): static
    {
        $this->contentKeys = $contentKeys;
        return $this;
    }
}