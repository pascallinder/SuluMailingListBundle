<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter\Mail;
use Doctrine\Common\Collections\ArrayCollection;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMailTranslation;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

readonly class NewsletterMailPreviewObjectProvider implements PreviewObjectProviderInterface
{
    public function __construct(
        private NewsletterMailRepository            $newsletterMailRepository,
        private NewsletterMailTranslationRepository $newsletterMailTranslationRepository,
        private MediaManagerInterface               $mediaManager
    ) {
    }
    public function getObject($id, $locale)
    {
        $newsletterMail= $this->newsletterMailRepository->findById($id,$locale);
        $newsletterMail->setTranslations(new ArrayCollection(
            array_reduce($this->newsletterMailTranslationRepository->findBy(['newsletterMail'=>$newsletterMail->getId()]),
            fn(array $carry, NewsletterMailTranslation $item)=>[...$carry,$item->getLocale()=>$item],[])));
        return $newsletterMail;
    }

    public function getId($object)
    {
        return $object->getId();
    }

    /**
     * @param NewsletterMail $object
     * @param string $locale
     */
    public function setValues($object, $locale, array $data)
    {
        $object->setMailTemplate($data['mailTemplate']);
        $object->setHeaderImage($data['headerImage'] && $data['headerImage']['id'] ?
            $this->mediaManager->getEntityById($data['headerImage']['id']): null);
        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        foreach ($data as $property => $value) {
            if($property === 'id' || !$propertyAccess->isWritable($object,$property)){
                continue;
            }
            try {
                $propertyAccess->setValue($object, $property, $value);
            } catch (\InvalidArgumentException $e) {
                //ignore not existing properties
            }
        }
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