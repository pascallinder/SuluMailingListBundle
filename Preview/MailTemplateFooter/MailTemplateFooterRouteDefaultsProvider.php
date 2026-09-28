<?php

namespace Linderp\SuluMailingListBundle\Preview\MailTemplateFooter;

use Linderp\SuluMailingListBundle\Controller\Admin\MailTemplateFooterController;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Repository\MailTemplateFooter\MailTemplateFooterRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailTemplateFooterRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly MailTemplateFooterRepository $repository) {}

    /** @return array<string, mixed> */
    public function getDefaults(Route $route): array
    {
        $template = $this->repository->findById((int) $route->getResourceId(), $route->getLocale());
        if (!$template instanceof MailTemplateFooter) {
            throw new NotFoundHttpException(sprintf('No mail footer found for id "%s".', $route->getResourceId()));
        }

        return [
            '_controller' => MailTemplateFooterController::class . '::indexAction',
            'object' => $template,
        ];
    }
}
