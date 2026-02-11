<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;

readonly class SubscriptionDocumentUrlProvider
{

    public function __construct(private DocumentManagerInterface $documentManager,
    private NewsletterRepository $newsletterRepository){
}

    /**
     * @throws DocumentManagerException
     */
    public function getUnsubscribePageUrl(string $newsletterId, string $locale):string{
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter->getUnsubscribePage(), $locale);
    }

    /**
     * @throws DocumentManagerException
     */
    public function getConfirmedDoubleOptPageUrl(string $newsletterId, string $locale):string{
        $newsletter = $this->newsletterRepository->findById((int) $newsletterId, $locale);
        return $this->getUrl($newsletter->getDoubleOptConfirmPage(), $locale);
    }

    /**
     * @throws DocumentManagerException
     */
    private function getUrl(string $documentUuid, string $locale): string
    {
        /** @var PageDocument $document */
        $document = $this->documentManager->find($documentUuid, $locale);
        return "/".$locale.$document->getResourceSegment();
    }
}
