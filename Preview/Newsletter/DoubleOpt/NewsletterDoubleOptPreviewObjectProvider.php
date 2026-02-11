<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;
use Doctrine\Common\Collections\ArrayCollection;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOptTranslation;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\Newsletter\MailTranslationPreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptTranslationRepository;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

readonly class NewsletterDoubleOptPreviewObjectProvider extends MailTranslationPreviewObjectProvider
{
    public function __construct(
        MailContextTypesPool $contextTypesPool,
        private NewsletterRepository                     $newsletterRepository,
        private NewsletterDoubleOptTranslationRepository $newsletterDoubleOptTranslationRepository
    ) {
        parent::__construct($contextTypesPool);
    }
    public function getObject($id, $locale): ?Newsletter
    {
        $newsletter = $this->newsletterRepository->findById((int) $id, $locale);
        if (!$newsletter instanceof Newsletter) {
            return null;
        }
        $newsletter->getNewsletterDoubleOpt()->setTranslations(new ArrayCollection(
            array_reduce($this->newsletterDoubleOptTranslationRepository->findBy(['newsletterDoubleOpt'=>$newsletter->getNewsletterDoubleOpt()->getId()]),
            fn(array $carry, NewsletterDoubleOptTranslation $item)=>[...$carry,$item->getLocale()=>$item],[])));
        return $newsletter;
    }

    /**
     * @param Newsletter $object
     * @param string $locale
     */
    /**
     * @param array<string, mixed> $data
     */
    /**
     * @param Newsletter $object
     * @param array<string, mixed> $data
     */
    public function setValues($object, $locale, array $data): void
    {
        $this->setMailTranslatableValues($object->getNewsletterDoubleOpt(),$data);
    }
}
