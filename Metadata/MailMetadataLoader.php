<?php

namespace Linderp\SuluMailingListBundle\Metadata;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypesPool;
use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceInterface;
use Linderp\SuluMailingListBundle\Mail\Resource\MailResourcePool;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypesPool;
use phpDocumentor\Reflection\Types\Boolean;
use Psr\Log\LoggerInterface;
use Sulu\Bundle\AdminBundle\FormMetadata\FormMetadataMapper;
use Sulu\Bundle\AdminBundle\FormMetadata\FormXmlLoader;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FieldMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadataLoaderInterface;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\LocalizedFormMetadataCollection;
use Sulu\Bundle\AdminBundle\Metadata\MetadataInterface;
use Sulu\Bundle\FormBundle\Metadata\PropertiesXmlLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class MailMetadataLoader implements FormMetadataLoaderInterface, CacheWarmerInterface
{
    /** @var MailFieldTypeInterface[] $sortedMailFieldTypes*/
    private array $sortedMailFieldTypes;
    /** @var MailWrapperTypeInterface[] $sortedMailWrapperTypes*/
    private array $sortedMailWrapperTypes;
    public function __construct(
        MailFieldTypesPool $mailFieldTypesPool,
        MailWrapperTypesPool $mailWrapperTypesPool,
        private MailResourcePool $mailResourcePool,
        #[Autowire('@sulu_admin.form_metadata.form_xml_loader')]
        private FormXmlLoader $formXmlLoader,
        #[Autowire('@sulu_form.metadata.properties_xml_loader')]
        private PropertiesXmlLoader $propertiesXmlLoader,
        #[Autowire('@sulu_admin.form_metadata.form_metadata_mapper')]
        private FormMetadataMapper $formMetadataMapper,
        private TranslatorInterface $translator,
        #[Autowire('%kernel.cache_dir%/sulu-mailing-list-bundle/forms')]
        private string $cacheDir,
        #[Autowire('%kernel.debug%')]
        private bool $debug
    ) {
        $this->sortedMailFieldTypes = $mailFieldTypesPool->getAllSorted();
        $this->sortedMailWrapperTypes = $mailWrapperTypesPool->getAllSorted();
    }
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @throws \Exception
     */
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $resources = $this->mailResourcePool->getAll();
        foreach ($resources as $resource) {
            $resourceConfig = $resource->getConfiguration();
            /** @var LocalizedFormMetadataCollection $formMetadataCollection */
            $formMetadataCollection = $this->formXmlLoader->load($resourceConfig->getXmlPath());
            foreach ($formMetadataCollection->getItems() as $locale => $formMetadata) {
                $content = new FieldMetadata('content');
                $content->setType('block');

                foreach ($this->sortedMailWrapperTypes as $wrapperType) {
                    $content->addType($this->createWrappersMetadata($wrapperType, $locale, $resource));
                }
                $content->setDefaultType('single-column-section');
                $content->setDisabledCondition('sent');
                $content->setMinOccurs(1);

                $formMetadata->addItem($content);
                $configCache = $this->getConfigCache($formMetadata->getKey(), $locale);
                $configCache->write(\serialize($formMetadata), [new FileResource($resourceConfig->getXmlPath())]);
            }
        }
        return [];
    }
    /**
     * @throws \Exception
     */
    public function getMetadata(string $key, string $locale, array $metadataOptions = []): ?MetadataInterface
    {
        $configCache = $this->getConfigCache($key, $locale);

        if (!\file_exists($configCache->getPath())) {
            return null;
        }

        if (!$configCache->isFresh()) {
            $this->warmUp($this->cacheDir);
        }

        return \unserialize(\file_get_contents($configCache->getPath()));
    }

    /**
     * @throws \Exception
     */
    private function createWrappersMetadata(MailWrapperTypeInterface $mailWrapperType, string $locale,
                                            MailResourceInterface $mailResource): FormMetadata
    {
        $wrapperForm = new FormMetadata();

        $configuration = $mailWrapperType->getConfiguration();
        $wrapperForm->setTitle($this->translator->trans($configuration->getTitle(), [], 'admin', $locale));
        $properties = $this->propertiesXmlLoader->load($configuration->getXmlPath());

        $wrapperForm->setItems($this->formMetadataMapper->mapChildren($properties->getProperties(), $locale));
        $wrapperForm->setName($mailWrapperType->getConfiguration()->getKey());
        foreach ($mailWrapperType->getConfiguration()->getContentKeys() as $label => $key) {
            $wrapperForm->addItem($this->createComponentsMetadata($key, $label, $locale, $mailResource::class, $mailWrapperType::class));
        }
        return $wrapperForm;
    }
    /**
     * @throws \Exception
     */
    private function createComponentsMetadata(string $contentKey, string $label, string $locale, string $resourceClass, string $wrapperClass): FieldMetadata{
        $components = new FieldMetadata($contentKey);
        $components->setType('block');
        $components->setLabel($this->translator->trans($label, [], 'admin', $locale));
        $fieldTypeMetaDataCollection = [];

        foreach ($this->sortedMailFieldTypes as $type) {
            if(count($type->getConfiguration()->getAcceptedResources()) &&
                !in_array($resourceClass,$type->getConfiguration()->getAcceptedResources(),true)){
                continue;
            }
            if(count($type->getConfiguration()->getAcceptedWrapper()) &&
                !in_array($wrapperClass,$type->getConfiguration()->getAcceptedWrapper(),true)){
                continue;
            }
            $fieldTypeMetaDataCollection[] = $this->loadFieldTypeMetadata($type->getConfiguration()->getKey(), $type, $locale);
        }
        foreach ($fieldTypeMetaDataCollection as $fieldTypeMetaData) {
            $components->addType($fieldTypeMetaData);
        }
        $components->setDefaultType(\current($components->getTypes())->getName());
        $components->setMinOccurs(1);
        return $components;
    }
    /**
     * @throws \Exception
     */
    private function loadFieldTypeMetadata(string $typeKey, MailWrapperTypeInterface|MailFieldTypeInterface $type, string $locale): FormMetadata
    {
        $form = new FormMetadata();
        $configuration = $type->getConfiguration();
        $properties = $this->propertiesXmlLoader->load($configuration->getXmlPath());

        $form->setItems($this->formMetadataMapper->mapChildren($properties->getProperties(), $locale));
        $form->setName($typeKey);
        $form->setTitle($this->translator->trans($configuration->getTitle(), [], 'admin', $locale));
        return $form;
    }
    private function getConfigCache(string $key, string $locale): ConfigCache
    {
        return new ConfigCache(\sprintf('%s%s%s.%s', $this->cacheDir, \DIRECTORY_SEPARATOR, $key, $locale), $this->debug);
    }
}