<?php

namespace Linderp\SuluMailingListBundle\Preview\MailTemplateHeader;

use Linderp\SuluMailingListBundle\Controller\Admin\MailTemplateHeaderController;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Repository\MailTemplateHeader\MailTemplateHeaderRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailTemplateHeaderRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly MailTemplateHeaderRepository $repository) {}

    /** @return array<string, mixed> */
    public function getDefaults(Route $route): array
    {
        $template = $this->repository->findById((int) $route->getResourceId(), $route->getLocale());
        if (!$template instanceof MailTemplateHeader) {
            throw new NotFoundHttpException(sprintf('No mail header found for id "%s".', $route->getResourceId()));
        }

        return [
            '_controller' => MailTemplateHeaderController::class . '::indexAction',
            'object' => $template,
        ];
    }
}
