<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;

use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypesPool;
use Linderp\SuluMailingListBundle\Mail\Font\MailFontInterface;
use Linderp\SuluMailingListBundle\Mail\Font\MailFontPool;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypesPool;
use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class MailContentProvider
{
    public static string $EXTENSIONS = 'mjml.twig';

    public function __construct(private readonly Environment          $twig,
                                #[Autowire('%sulu_mailing_list.mjml.caching%')]
                                private readonly bool                 $cachingEnabled,
                                #[Autowire('%sulu_mailing_list.mjml.icons_path%')]
                                private readonly ?string $iconsPath,
                                private readonly CacheInterface       $cache,
                                private readonly MailFontPool         $mailFontPool,
                                private readonly MailFieldTypesPool   $mailFieldTypesPool,
                                private readonly MailWrapperTypesPool $mailWrapperTypesPool,
                                private readonly MailContextTypesPool $mailContextTypesPool,
                                private readonly MjmlAPIService       $mjmlAPIService)
    {

    }

    /**
     * @throws InvalidArgumentException
     */
    /**
     * @param array<string, mixed> $data
     */
    public function getMailTranslatableMailContent(MailTranslatable $mailTranslatable, string $locale, array $data): string
    {
        return $this->getCachingMailContent('@SuluMailingList/mails/email', $locale,[
            ...$this->getMailTranslateData($mailTranslatable,$locale),
            ...$data,
        ], $mailTranslatable);
    }
    /**
     * @throws InvalidArgumentException
     */
    /**
     * @param array<string, mixed> $data
     */
    public function getCachingMailContent(string $mailTemplate, string $locale, array $data, ?MailTranslatable $mailTranslatable = null): string
    {
        $replaceableContent = [];
        foreach ($data as $key => $value) {
            if($key === 'content'){
                continue;
            }
            $contextVars = $mailTranslatable?->getContextVars() ?? [];
            if(array_key_exists($key, $contextVars)){
                continue;
            }
            $replaceableContent[$key] = '{{ ' . $key . ' }}';
        }

        $fonts = array_reduce($this->mailFontPool->getAll(),fn(array $carry,MailFontInterface $font) => [...$carry,$font->getConfiguration()],[]);
        $contentGenerator = function () use ($mailTranslatable, $fonts, $data, $replaceableContent, $mailTemplate, $locale) {
            $contextVars = $mailTranslatable?->getContextVars() ?? [];
            $mjmlContent = $this->twig->render($mailTemplate . '.'
                . self::$EXTENSIONS, [...$replaceableContent,
                "content" => $data['content'],
                "fonts" => $fonts,
                "iconsPath" => $this->iconsPath,
                'locale' => $locale,
                ...array_reduce(array_keys($contextVars), fn(array $carry, string $key): array => [...$carry, $key => $data[$key] ?? null], []),
            ]);

            return $this->mjmlAPIService->render($mjmlContent);
        };
        if($this->cachingEnabled){
            $templateCacheKey = 'mail_translatable_' . hash('sha256', $mailTemplate . $locale
                    . json_encode($data['content']) . json_encode($fonts)
                    . array_reduce(array_keys($mailTranslatable?->getContextVars() ?? []),
                        fn(string $carry, string $key): string => $carry . json_encode($data[$key] ?? null), ''));
            $html = $this->cache->get($templateCacheKey,$contentGenerator );
        }else{
            $html = $contentGenerator();
        }
        unset($data['content']);
        if ($mailTranslatable) {
            foreach ($mailTranslatable->getContextVars() ?? [] as $key => $value) {
                unset($data[$key]);
            }
        }
        $search  = array_values($replaceableContent);
        $replace = array_values($data);
        return str_replace($search, $replace, $html);
    }

    /**
     * @return array<string, mixed>
     */
    private function getMailTranslateData(MailTranslatable $mailTranslatable, string $locale): array{
        $content = $mailTranslatable->getContent();

        if ($content === null) {
            return ['content' => []];
        }

        $translatedContent = array_map(function (array $wrapper) use ($locale): array {
            $wrapperType = $this->mailWrapperTypesPool->get($wrapper['type']);
            $wrapperConfig = $wrapperType->getConfiguration();

            $result = $wrapperType->build($wrapper, $locale);

            foreach ($wrapperConfig->getContentKeys() as $key) {
                $result[$key] = array_map(
                    function (array $component) use ($locale): array {
                        $fieldType = $this->mailFieldTypesPool->get($component['type']);
                        return $fieldType->build($component, $locale);
                    },
                    $wrapper[$key] ?? []
                );
            }

            return $result;
        }, $content);

        $data = [
            'content' => $translatedContent,
            'context' => $mailTranslatable->getContext(),
        ];

        $context = $mailTranslatable->getContext();
        $contextVars = $mailTranslatable->getContextVars();

        if ($context && $contextVars) {
            $data = array_replace(
                $data,
                $this->mailContextTypesPool->get($context)->build($contextVars, $locale)
            );
        }

        return $data;
    }
}
