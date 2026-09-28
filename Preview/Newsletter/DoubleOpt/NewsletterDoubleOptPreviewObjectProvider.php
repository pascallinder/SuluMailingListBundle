<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Controller\Admin\NewsletterController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOptTranslation;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\Newsletter\MailTranslationPreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptTranslationRepository;

readonly class NewsletterDoubleOptPreviewObjectProvider extends MailTranslationPreviewObjectProvider
{
    public function __construct(
        MailContextTypesPool $contextTypesPool,
        ManagerRegistry $managerRegistry,
        private NewsletterRepository                     $newsletterRepository,
        private NewsletterDoubleOptTranslationRepository $newsletterDoubleOptTranslationRepository
    ) {
        parent::__construct($contextTypesPool, $managerRegistry);
    }
    public function getObject(int|string $id, string $locale): ?Newsletter
    {
        $newsletter = $this->newsletterRepository->findById((int) $id, $locale);
        if (!$newsletter instanceof Newsletter) {
            return null;
        }
        $newsletter->getNewsletterDoubleOpt()->setTranslations(new ArrayCollection(
            array_reduce(
                $this->newsletterDoubleOptTranslationRepository->findBy(['newsletterDoubleOpt' => $newsletter->getNewsletterDoubleOpt()->getId()]),
                fn(array $carry, NewsletterDoubleOptTranslation $item) => [...$carry,$item->getLocale() => $item],
                []
            )
        ));
        return $newsletter;
    }

    /** @param array<string, mixed> $data */
    public function setValues(object $object, string $locale, array $data): void
    {
        if (!$object instanceof Newsletter) {
            throw new \InvalidArgumentException('Expected a newsletter preview object.');
        }

        $this->setMailTranslatableValues($object->getNewsletterDoubleOpt(), $data);
    }

    public function getPreviewController(): string
    {
        return NewsletterController::class . '::indexAction';
    }
}
