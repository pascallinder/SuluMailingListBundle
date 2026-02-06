<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\MailTypesPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class MailFieldTypesPool implements MailTypesPoolInterface
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
    public function getAllSorted(): array{
        $values = $this->getAll();
        \usort($values, static function(MailFieldTypeInterface $a, MailFieldTypeInterface $b): int {
            $aConfig = $a->getConfiguration();
            $bConfig = $b->getConfiguration();
            $priorityCompare = $aConfig->getPriority() <=> $bConfig->getPriority();
            if ($priorityCompare !== 0) {
                return $priorityCompare;
            }
            return strcmp($aConfig->getTitle(), $bConfig->getTitle());
        });
        return $values;
    }
    public function get(string $typeKey): MailFieldTypeInterface{
        return $this->mailFieldTypes[$typeKey];
    }
}