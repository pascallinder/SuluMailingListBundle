<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter;

use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Sulu\Bundle\PreviewBundle\Preview\PreviewContext;
use Sulu\Bundle\PreviewBundle\Preview\Provider\PreviewDefaultsProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract readonly class MailTranslationPreviewObjectProvider implements PreviewDefaultsProviderInterface
{
    public function __construct(
        private MailContextTypesPool $contextTypesPool,
    ) {}
    /**
     * @param array<string, mixed> $data
     */
    public function setMailTranslatableValues(MailTranslatable $object, array $data): void
    {
        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        foreach ($data as $property => $value) {
            if ($property === 'id' || !$propertyAccess->isWritable($object, $property)) {
                continue;
            }
            try {
                $propertyAccess->setValue($object, $property, $value);
            } catch (\InvalidArgumentException $e) {
            }
        }
        $object->setContent($data['content_' . $object->getContext()]);
        $keys = $this->contextTypesPool->get($data['context'])->getConfiguration()->getContextVarsKeys();
        $object->setContextVars(array_reduce($keys, fn($carry, $key) => [...$carry, $key => $data[$key]], []));
    }

    public function getDefaults(PreviewContext $previewContext): array
    {
        $object = $this->getObject($previewContext->getId(), $previewContext->getLocale());
        if (!\is_object($object)) {
            return [];
        }

        return [
            'object' => $object,
            '_controller' => $this->getPreviewController(),
        ];
    }

    public function updateValues(PreviewContext $previewContext, array $defaults, array $data): array
    {
        if (isset($defaults['object']) && \is_object($defaults['object'])) {
            $this->setValues($defaults['object'], $previewContext->getLocale(), $data);
        }

        return $defaults;
    }

    public function updateContext(PreviewContext $previewContext, array $defaults, array $context): array
    {
        return $defaults;
    }

    public function getSecurityContext(PreviewContext $previewContext): ?string
    {
        return null;
    }

    abstract public function getObject($id, $locale): mixed;

    abstract public function getPreviewController(): string;

    abstract public function setValues($object, $locale, array $data): void;

}
