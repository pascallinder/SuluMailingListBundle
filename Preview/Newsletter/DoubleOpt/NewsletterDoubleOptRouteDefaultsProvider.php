<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;

use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterController;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;

class NewsletterDoubleOptRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly NewsletterRepository $repository) {}

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(Route $route): array
    {
        return [
            '_controller' => NewsletterController::class . '::indexAction',
            'newsletter' => $this->repository->findById((int) $route->getResourceId(), $route->getLocale()),
        ];
    }
}
