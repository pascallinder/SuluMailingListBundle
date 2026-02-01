<?php

namespace Linderp\SuluMailingListBundle\Mail\Wrapper;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class MailWrapperTypesPool implements MailPoolInterface
{
    /** @var array<string,MailWrapperTypeInterface> $mailWrapperTypes */
    private array $mailWrapperTypes;
    public function __construct(

        #[AutowireIterator('mailing.wrapper-type')]
        iterable $handlers
    ) {
        $this->mailWrapperTypes = [];
        /** @var MailWrapperTypeInterface $handler */
        foreach ($handlers as $handler) {
            $this->mailWrapperTypes[$handler->getConfiguration()->getKey()] = $handler;
        }
    }

    /**
     * @return MailWrapperTypeInterface[]
     */
    public function getAll(): array
    {
        return array_values($this->mailWrapperTypes);
    }
    public function get(string $typeKey): MailWrapperTypeInterface{
        return $this->mailWrapperTypes[$typeKey];
    }
}