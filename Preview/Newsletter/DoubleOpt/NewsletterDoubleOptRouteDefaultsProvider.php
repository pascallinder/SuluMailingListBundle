<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;

use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterDoubleOptRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly NewsletterRepository $repository) {}

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(Route $route): array
    {
        $newsletter = $this->repository->findById((int) $route->getResourceId(), $route->getLocale());
        if (!$newsletter instanceof Newsletter) {
            throw new NotFoundHttpException(\sprintf('No newsletter found for id "%s".', $route->getResourceId()));
        }

        return [
            '_controller' => NewsletterController::class . '::indexAction',
            'object' => $newsletter,
        ];
    }
}
