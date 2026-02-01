<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\DoubleOpt;
use Doctrine\Common\Collections\ArrayCollection;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOptTranslation;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptTranslationRepository;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

readonly class NewsletterDoubleOptPreviewObjectProvider implements PreviewObjectProviderInterface
{
    public function __construct(
        private NewsletterRepository                     $newsletterRepository,
        private NewsletterDoubleOptTranslationRepository $newsletterDoubleOptTranslationRepository
    ) {
    }
    public function getObject($id, $locale): Newsletter|null
    {
        $newsletter= $this->newsletterRepository->findById($id,$locale);
        $newsletter->getNewsletterDoubleOpt()->setTranslations(new ArrayCollection(
            array_reduce($this->newsletterDoubleOptTranslationRepository->findBy(['newsletterDoubleOpt'=>$newsletter->getNewsletterDoubleOpt()->getId()]),
            fn(array $carry, NewsletterDoubleOptTranslation $item)=>[...$carry,$item->getLocale()=>$item],[])));
        return $newsletter;
    }

    public function getId($object)
    {
        return $object->getId();
    }

    /**
     * @param Newsletter $object
     * @param string $locale
     */
    public function setValues($object, $locale, array $data): void
    {
        $object->getNewsletterDoubleOpt()->setContent($data['content']);
        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();
        $doubleOptObject = $object->getNewsletterDoubleOpt();
        $doubleOptObject->setContent($data['content']);
        foreach ($data as $property => $value) {
            if(!str_contains($property,'doubleOpt_')){
                continue;
            }

            $property=str_replace('doubleOpt_','',$property);
            if($property === 'id' || !$propertyAccess->isWritable($doubleOptObject,$property)){
                continue;
            }
            try {
                $propertyAccess->setValue($doubleOptObject, $property, $value);
            } catch (\InvalidArgumentException $e) {
                //ignore not existing properties
            }
        }
        $object->setNewsletterDoubleOpt($doubleOptObject);

    }

    public function setContext($object, $locale, array $context)
    {
        // TODO: Implement setContext() method.
    }

    public function serialize($object)
    {
        return serialize($object);
    }

    public function deserialize($serializedObject, $objectClass)
    {
        return unserialize($serializedObject);
    }

    public function getSecurityContext($id, $locale): ?string
    {
        return null;
    }
}