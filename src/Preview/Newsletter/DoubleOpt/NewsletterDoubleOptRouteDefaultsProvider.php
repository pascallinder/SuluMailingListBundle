<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;
use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class NewsletterDoubleOptRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function getByEntity($entityClass, $id, $locale, $object = null): array
    {

        return [
            '_controller' => NewsletterController::class.'::indexAction',
            'newsletter'=>$object
        ];
    }

    public function isPublished($entityClass, $id, $locale): bool
    {
        return true;
    }

    public function supports($entityClass): bool
    {
        return $entityClass === Newsletter::class;
    }
}