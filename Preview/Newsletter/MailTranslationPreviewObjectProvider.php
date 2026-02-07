<?php
namespace Linderp\SuluMailingListBundle\Preview\Newsletter;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract readonly class MailTranslationPreviewObjectProvider implements PreviewObjectProviderInterface
{
    public function __construct(
        private MailContextTypesPool $contextTypesPool,
    ){

    }
    public function setMailTranslatableValues(MailTranslatable $object, array $data): void
    {
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
            }
        }
        $object->setContent($data['content_'.$object->getContext()]);
        $keys = $this->contextTypesPool->get($data['context'])->getConfiguration()->getContextVarsKeys();
        $object->setContextVars(array_reduce($keys,fn($carry, $key) => [...$carry, $key =>$data[$key]],[]));
    }

    public function setContext($object, $locale, array $context)
    {}

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

    public function getId($object)
    {
        return $object->getId();
    }
}