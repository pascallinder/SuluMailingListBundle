<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription\FieldHandler\Special;
use Linderp\SuluFormSaveContactBundle\Service\FieldHandler\FieldHandler;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;

class NewsletterFieldHandler extends FieldHandler
{
    public function __construct(private readonly NewsletterRepository $newsletterRepository){

    }

    protected function handleField(array $field, array $data): array
    {
        if(!array_key_exists('options',$field)
            || !array_key_exists('newsletterId',$field['options'])){
            return $data;
        }
        if($field['value']){
            $data[self::getPropertyName()] = $this->newsletterRepository->find(
                $field['options']['newsletterId']);
        }
        return $data;
    }

    protected function getFieldType(): string
    {
        return 'newsletter';
    }

    public static function getPropertyName(): string
    {
        return 'newsletter';
    }
}