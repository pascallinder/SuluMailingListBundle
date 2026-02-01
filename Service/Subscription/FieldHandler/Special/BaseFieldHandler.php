<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription\FieldHandler\Special;
use Linderp\SuluFormSaveContactBundle\Service\FieldHandler\FieldHandler;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;

abstract class BaseFieldHandler extends FieldHandler
{
    public function __construct(private readonly NewsletterRepository $newsletterRepository){

    }
    protected function handleField(array $field, array $data): array
    {

        if (!isset($field['options']['newsletterId'])) {
            return $data;
        }
        if(!isset($data[static::getPropertyName()])){
            $data[static::getPropertyName()]=[];
        }

        if(($field['value'] === true) === $this->valueRequired()){
            $data[static::getPropertyName()][] = $this->newsletterRepository->find(
                $field['options']['newsletterId']);
        }
        return $data;
    }
    public static function getPropertyName(): string
    {
        return 'newsletters';
    }
    protected abstract function valueRequired(): bool;
}