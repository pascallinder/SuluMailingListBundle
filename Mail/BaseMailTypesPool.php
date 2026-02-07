<?php

namespace Linderp\SuluMailingListBundle\Mail;

/**
 * @template T of MailTypeInterface
 */
abstract class BaseMailTypesPool implements MailPoolInterface
{
    /** @var array<string,T> $types */
    private array $types;
    public function __construct(
        iterable $handlers
    ) {
        $this->types = [];
        /** @var T $handler */
        foreach ($handlers as $handler) {
            $this->types[$handler->getConfiguration()->getKey()] = $handler;
        }
    }

    /**
     * @return T
     */
    public function get(string $typeKey): MailTypeInterface
    {
        return $this->types[$typeKey];
    }
    public function getAll(): array
    {
        return array_values($this->types);
    }
    /**
     * @return T[]
     */
    public function getAllSorted(): array{
        $values = $this->getAll();
        \usort($values, static function(MailTypeInterface $a, MailTypeInterface $b): int {
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
}