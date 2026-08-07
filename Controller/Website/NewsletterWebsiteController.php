<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Controller\Website;

use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionDocumentUrlProvider;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsletterWebsiteController extends AbstractController
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly SubscriptionDocumentUrlProvider $subscriptionDocumentUrlProvider
    ) {}

    #[Route('{locale}/newsletter/unsubscribe/{newsletterId}/{token}', name: 'app.newsletter.unsubscribe')]
    public function unsubscribe(string $locale, string $newsletterId, string $token): Response
    {
        if ($this->subscriptionService->unsubscribe($newsletterId, $token)) {
            return $this->redirect($this->subscriptionDocumentUrlProvider->getUnsubscribePageUrl($newsletterId, $locale));
        }
        return $this->redirect("/" . $locale);
    }
    #[Route('{locale}/newsletter/confirm/{newsletterId}/{token}', name: 'app.newsletter.confirm')]
    public function doubleOptConfirm(string $locale, string $newsletterId, string $token): Response
    {
        if ($this->subscriptionService->confirmDoubleOpt($newsletterId, $token)) {
            return $this->redirect($this->subscriptionDocumentUrlProvider->getConfirmedDoubleOptPageUrl($newsletterId, $locale));
        }
        return $this->redirect("/" . $locale);
    }
}
