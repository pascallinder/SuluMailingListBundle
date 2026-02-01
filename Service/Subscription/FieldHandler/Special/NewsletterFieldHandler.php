<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription\FieldHandler\Special;

class NewsletterFieldHandler extends BaseFieldHandler
{
    protected function getFieldType(): string
    {
        return 'newsletter';
    }
    protected function valueRequired(): bool
    {
        return true;
    }
}