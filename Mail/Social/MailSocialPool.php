<?php

namespace Linderp\SuluMailingListBundle\Mail\Social;

use Linderp\SuluMailingListBundle\Mail\MailPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsAlias(id: 'mailing.social-pool', public: true)]
class MailSocialPool implements MailPoolInterface
{
    /** @var MailSocialInterface[] */
    private array $socials = [];
    /**
     * @param iterable<MailSocialInterface> $socials
     * @param array<string, array<string, mixed>> $socialsYamls
     */
    public function __construct(
        #[AutowireIterator('mailing.social')]
        iterable $socials,
        #[Autowire('%sulu_mailing_list.mjml.socials%')]
        array $socialsYamls,
        private readonly TranslatorInterface $translator,
    ) {
        foreach ($socialsYamls as $name => $socialYamlElement) {
            $this->socials[$name] = new InternalMailSocial(
                new InternalMailSocialConfiguration(
                    $name,
                    'mailingListMail.props.content.social.element.'.$name,
                    $socialYamlElement['src'] ?? null
                )
            );
        }
        /** @var MailSocialInterface $social */
        foreach ($socials as $social) {
            $this->socials[$social->getConfiguration()->getName()] = $social;
        }

    }
    /**
     * @return array<string, MailSocialInterface|InternalMailSocial>
     */
    public function getAll(): array{
        return $this->socials;
    }

    public function getOne(string $name): MailSocialInterface|InternalMailSocial
    {
        return $this->socials[$name];
    }

    /**
     * @return list<array{name: string, title: string}>
     */
    public function getSocialSelection(string $locale): array{
        return array_reduce($this->socials, fn(array $carry, MailSocialInterface|InternalMailSocial $social)=> [...$carry,[
            'name'=> $social->getConfiguration()->getName(),
            'title'=>$this->translator->trans($social->getConfiguration()->getTitle(),[],'admin',$locale)
        ]], []);
    }

    public function getDefaultValue(): ?string{
        if(count($this->socials) === 0){
            return null;
        }
        return \current($this->socials)->getConfiguration()->getName();
    }
}
