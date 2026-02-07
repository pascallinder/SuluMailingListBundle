<?php

namespace Linderp\SuluMailingListBundle\Mail\Context;

use Linderp\SuluMailingListBundle\Mail\BaseMailTypesPool;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @extends BaseMailTypesPool<MailContextTypeInterface>
 */
class MailContextTypesPool extends BaseMailTypesPool
{
    public function __construct(

        #[AutowireIterator('mailing.context-type')]
        iterable $handlers
    ) {
        parent::__construct($handlers);
    }
}