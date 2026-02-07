<?php

namespace Linderp\SuluMailingListBundle\Mail\Wrapper;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Linderp\SuluMailingListBundle\Mail\BaseMailTypesPool;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @extends BaseMailTypesPool<MailWrapperTypeInterface>
 */
class MailWrapperTypesPool extends BaseMailTypesPool
{
    public function __construct(
        #[AutowireIterator('mailing.wrapper-type')]
        iterable $handlers
    ) {
        parent::__construct($handlers);
    }
}