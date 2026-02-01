<?php

namespace Linderp\SuluMailingListBundle\Content\Select;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Contracts\Translation\TranslatorInterface;
#[AsAlias(id: 'sulu_mailing_list.salutation_prefix_select', public: true)]
readonly class SalutationPrefixSelect
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    public function getValues($locale):array{
        return [
            [
                'name' => '0',
                'title' => $this->translator->trans('mailingListMail.props.content.salutation.prefix.option1', [], 'admin', $locale),
            ],
            [
                'name' => '1',
                'title' => $this->translator->trans('mailingListMail.props.content.salutation.prefix.option2', [], 'admin', $locale),
            ],
            [
                'name' => '2',
                'title' => $this->translator->trans('mailingListMail.props.content.salutation.prefix.option3', [], 'admin', $locale),
            ],
        ];
    }
    public function getValue(int $index, string $locale):string{
        return $this->getValues($locale)[$index]['title'] ?? '';
    }
    public function getDefaultValue():string{
        return '0';
    }
}