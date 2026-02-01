<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;
use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterMailController;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class NewsletterMailRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function getByEntity($entityClass, $id, $locale, $object = null): array
    {

        return [
            '_controller' => NewsletterMailController::class.'::indexAction',
            'mail'=>$object
        ];
    }

    public function isPublished($entityClass, $id, $locale): bool
    {
        return true;
    }

    public function supports($entityClass): bool
    {
        return $entityClass === NewsletterMail::class;
    }
}