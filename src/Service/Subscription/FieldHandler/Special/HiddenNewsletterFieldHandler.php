<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription\FieldHandler\Special;

class HiddenNewsletterFieldHandler extends BaseFieldHandler
{
    protected function getFieldType(): string
    {
        return 'hidden_newsletter';
    }

    protected function valueRequired(): bool
    {
        return false;
    }

}