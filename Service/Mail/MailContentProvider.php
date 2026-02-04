<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;

use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
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

    public function __construct(private readonly Environment $twig,
                                #[Autowire('%sulu_mailing_list.mjml.caching%')]
                                private readonly bool $cachingEnabled,
                                private readonly CacheInterface $cache,
                                private readonly MailFontPool $mailFontPool,
                                private readonly MailFieldTypesPool $mailFieldTypesPool,
                                private readonly MailWrapperTypesPool $mailWrapperTypesPool,
                                private readonly MjmlAPIService $mjmlAPIService)
    {

    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMailTranslatableMailContent(MailTranslatable $mailTranslatable, string $locale, array $data): string
    {
        return $this->getCachingMailContent('@SuluMailingList/mails/email', $locale,[
            ...$this->getMailTranslateData($mailTranslatable,$locale),
            ...$data
        ]);
    }
    /**
     * @throws InvalidArgumentException
     */
    public function getCachingMailContent(string $mailTemplate, string $locale, array $data):string
    {
        $replaceableContent = [];
        foreach ($data as $key => $value) {
            if($key === 'content'){
                continue;
            }
            $replaceableContent[$key] = '{{ ' . $key . ' }}';
        }

        $fonts = array_reduce($this->mailFontPool->getAll(),fn(array $carry,MailFontInterface $font) => [...$carry,$font->getConfiguration()],[]);
        $contentGenerator = function () use ($fonts, $data, $replaceableContent, $mailTemplate, $locale) {
            $mjmlContent = $this->twig->render($mailTemplate . '.'
                . self::$EXTENSIONS, [...$replaceableContent,
                "content" => $data['content'],
                "fonts" => $fonts,
                'locale' => $locale
            ]);
            return $this->mjmlAPIService->render($mjmlContent);
        };
        if($this->cachingEnabled){
            $templateCacheKey = 'mail_translatable_' . hash('sha256', $mailTemplate . $locale. json_encode($data['content']) . json_encode($fonts));
            $html = $this->cache->get($templateCacheKey,$contentGenerator );
        }else{
            $html = $contentGenerator();
        }
        unset($data['content']);
        $search  = array_values($replaceableContent);
        $replace = array_values($data);
        return str_replace($search, $replace, $html);
    }

    private function getMailTranslateData(MailTranslatable $mailTranslatable, string $locale):array{
        if($mailTranslatable->getContent() === null){
            return ['content'=>[]];
        }
        return ['content' => array_map(function($wrapper) use ($locale) {
            return [
                ...$this->mailWrapperTypesPool->get($wrapper['type'])->build($wrapper,$locale),
                "components" => array_map(fn($component)=> $this->mailFieldTypesPool
                        ->get($component['type'])->build($component,$locale), $wrapper['components'])
            ];
        }, $mailTranslatable->getContent())];
    }
}