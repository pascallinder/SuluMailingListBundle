<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterMailController;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMailTranslation;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\Newsletter\MailTranslationPreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;

readonly class NewsletterMailPreviewObjectProvider extends MailTranslationPreviewObjectProvider
{
    public function __construct(
        MailContextTypesPool                        $contextTypesPool,
        ManagerRegistry                             $managerRegistry,
        private NewsletterMailRepository            $newsletterMailRepository,
        private NewsletterMailTranslationRepository $newsletterMailTranslationRepository
    ) {
        parent::__construct($contextTypesPool, $managerRegistry);
    }
    public function getObject(int|string $id, string $locale): NewsletterMail
    {
        $newsletterMail = $this->newsletterMailRepository->findById((int) $id, $locale);
        if (!$newsletterMail instanceof NewsletterMail) {
            throw new \RuntimeException('Newsletter mail not found.');
        }
        $newsletterMail->setTranslations(new ArrayCollection(
            array_reduce(
                $this->newsletterMailTranslationRepository->findBy(['newsletterMail' => $newsletterMail->getId()]),
                fn(array $carry, NewsletterMailTranslation $item) => [...$carry,$item->getLocale() => $item],
                []
            )
        ));
        return $newsletterMail;
    }

    /** @param array<string, mixed> $data */
    public function setValues(object $object, string $locale, array $data): void
    {
        if (!$object instanceof NewsletterMail) {
            throw new \InvalidArgumentException('Expected a newsletter mail preview object.');
        }

        $this->setMailTranslatableValues($object, $data);
    }

    public function getPreviewController(): string
    {
        return NewsletterMailController::class . '::indexAction';
    }
}
