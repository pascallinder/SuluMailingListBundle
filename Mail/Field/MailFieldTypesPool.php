<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class MailFieldTypesPool implements MailPoolInterface
{
    /** @var array<string,MailFieldTypeInterface> $mailFieldTypes */
    private array $mailFieldTypes;
    public function __construct(

        #[AutowireIterator('mailing.field-type')]
        iterable $handlers
    ) {
        $this->mailFieldTypes = [];
        /** @var MailFieldTypeInterface $handler */
        foreach ($handlers as $handler) {
            $this->mailFieldTypes[$handler->getConfiguration()->getKey()] = $handler;
        }
    }

    /**
     * @return MailFieldTypeInterface[]
     */
    public function getAll(): array
    {
        return array_values($this->mailFieldTypes);
    }
    public function get(string $typeKey): MailFieldTypeInterface{
        return $this->mailFieldTypes[$typeKey];
    }
}