<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;

use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterMailController;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Sulu\Route\Application\Routing\Matcher\RouteDefaultsProviderInterface;
use Sulu\Route\Domain\Model\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterMailRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(private readonly NewsletterMailRepository $repository) {}

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(Route $route): array
    {
        $mail = $this->repository->findById((int) $route->getResourceId(), $route->getLocale());
        if (!$mail instanceof NewsletterMail) {
            throw new NotFoundHttpException(\sprintf('No newsletter mail found for id "%s".', $route->getResourceId()));
        }

        return [
            '_controller' => NewsletterMailController::class . '::indexAction',
            'object' => $mail,
        ];
    }
}
