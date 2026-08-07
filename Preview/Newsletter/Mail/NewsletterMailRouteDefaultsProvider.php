<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;
use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterMailController;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;

class NewsletterMailRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly NewsletterMailRepository $repository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(Route $route): array
    {
        return [
            '_controller' => NewsletterMailController::class.'::indexAction',
            'mail' => $this->repository->findById((int) $route->getResourceId(), $route->getLocale()),
        ];
    }
}
