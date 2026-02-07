<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;
use Doctrine\Common\Collections\ArrayCollection;
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
        private NewsletterMailRepository            $newsletterMailRepository,
        private NewsletterMailTranslationRepository $newsletterMailTranslationRepository
    ) {
        parent::__construct($contextTypesPool);
    }
    public function getObject($id, $locale): NewsletterMail
    {
        $newsletterMail= $this->newsletterMailRepository->findById($id,$locale);
        $newsletterMail->setTranslations(new ArrayCollection(
            array_reduce($this->newsletterMailTranslationRepository->findBy(['newsletterMail'=>$newsletterMail->getId()]),
            fn(array $carry, NewsletterMailTranslation $item)=>[...$carry,$item->getLocale()=>$item],[])));
        return $newsletterMail;
    }

    /**
     * @param NewsletterMail $object
     * @param string $locale
     */
    public function setValues($object, $locale, array $data): void
    {
        $this->setMailTranslatableValues($object, $data);
    }
}