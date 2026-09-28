<?php

namespace Linderp\SuluMailingListBundle\Preview\Newsletter;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTemplateAwareInterface;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Sulu\Bundle\PreviewBundle\Preview\PreviewContext;
use Sulu\Bundle\PreviewBundle\Preview\Provider\PreviewDefaultsProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract readonly class MailTranslationPreviewObjectProvider implements PreviewDefaultsProviderInterface
{
    public function __construct(
        private MailContextTypesPool $contextTypesPool,
        private ManagerRegistry $managerRegistry,
    ) {}
    /**
     * @param array<string, mixed> $data
     */
    public function setMailTranslatableValues(MailTranslatable $object, array $data): void
    {
        $context = $data['context'] ?? null;
        if (!is_string($context)) {
            throw new \InvalidArgumentException('Expected a valid mail context for preview.');
        }

        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        foreach ($data as $property => $value) {
            if (in_array($property, ['id', 'header', 'footer'], true)
                || !$propertyAccess->isWritable($object, $property)) {
                continue;
            }
            try {
                $propertyAccess->setValue($object, $property, $value);
            } catch (\InvalidArgumentException) {
                // @ignoreException
                // Invalid transient preview values must not prevent the remaining fields from rendering.
            }
        }
        $content = $data['content_' . $context] ?? null;
        $object->setContent(is_array($content) ? $content : null);
        $keys = $this->contextTypesPool->get($context)->getConfiguration()->getContextVarsKeys();
        $object->setContextVars(array_reduce($keys, fn($carry, $key) => [...$carry, $key => $data[$key] ?? null], []));

        if ($object instanceof MailTemplateAwareInterface) {
            $headerId = $data['header'] ?? null;
            $header = is_int($headerId)
                ? $this->managerRegistry->getRepository(MailTemplateHeader::class)->find($headerId)
                : null;
            $object->setHeader($header instanceof MailTemplateHeader ? $header : null);

            $footerId = $data['footer'] ?? null;
            $footer = is_int($footerId)
                ? $this->managerRegistry->getRepository(MailTemplateFooter::class)->find($footerId)
                : null;
            if ($footer instanceof MailTemplateFooter) {
                $object->setFooter($footer);
            }
        }
    }

    public function getDefaults(PreviewContext $previewContext): array
    {
        $id = $previewContext->getId();
        $locale = $previewContext->getLocale();
        if ((!is_int($id) && !is_string($id)) || !is_string($locale)) {
            return [];
        }

        $object = $this->getObject($id, $locale);
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
        $locale = $previewContext->getLocale();
        if (isset($defaults['object']) && \is_object($defaults['object']) && is_string($locale)) {
            $this->setValues($defaults['object'], $locale, $data);
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

    abstract public function getObject(int|string $id, string $locale): mixed;

    abstract public function getPreviewController(): string;

    /** @param array<string, mixed> $data */
    abstract public function setValues(object $object, string $locale, array $data): void;

}
