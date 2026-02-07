<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\BaseMailTypesPool;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @extends BaseMailTypesPool<MailFieldTypeInterface>
 */
class MailFieldTypesPool extends BaseMailTypesPool
{
    public function __construct(

        #[AutowireIterator('mailing.field-type')]
        iterable $handlers
    )
    {
        parent::__construct($handlers);
    }
}