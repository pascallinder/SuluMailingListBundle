<?php

namespace Linderp\SuluMailingListBundle\Mail\Resource;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class MailResourcePool implements MailPoolInterface
{
    /** @var MailResourceInterface[] $resources */
    private array $resources;
    public function __construct(

        #[AutowireIterator('mailing.resource')]
        iterable $resources
    ) {
        $this->resources =[...$resources];
    }

    /**
     * @return MailResourceInterface[]
     */
    public function getAll(): array
    {
        return $this->resources;
    }
}