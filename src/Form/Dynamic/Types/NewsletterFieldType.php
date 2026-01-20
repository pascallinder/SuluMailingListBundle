<?php

namespace Linderp\SuluMailingListBundle\Form\Dynamic\Types;
use Sulu\Bundle\FormBundle\Dynamic\FormFieldTypeConfiguration;
use Sulu\Bundle\FormBundle\Dynamic\FormFieldTypeInterface;
use Sulu\Bundle\FormBundle\Entity\FormField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsletterFieldType implements FormFieldTypeInterface
{
    public function __construct()
    {}

    public function getConfiguration(): FormFieldTypeConfiguration
    {
        return new FormFieldTypeConfiguration(
            'sulu_form.fields.type_newsletter_field',
            __DIR__ . '/../../../Resources/config/form-fields/newsletter_field_type.xml',
            'special'
        );
    }

    public function build(FormBuilderInterface $builder, FormField $field, string $locale, array $options): void
    {
        $type = CheckboxType::class;
        $builder->add($field->getKey(), $type, $options);
    }

    /**
     * @return mixed|string|null
     */
    public function getDefaultValue(FormField $field, string $locale): mixed
    {
        return $field->getTranslation($locale)?->getDefaultValue();
    }
}