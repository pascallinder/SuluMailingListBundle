<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;

use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class MailContentProvider
{
    public static string $EXTENSIONS = 'mjml.twig';

    public function __construct(private readonly Environment $twig,
                                private readonly CacheInterface $cache,
                                private readonly WebspaceManagerInterface $webspaceManager,
                                private readonly MediaManagerInterface $mediaManager,
                                private readonly MjmlAPIService $mjmlAPIService)
    {

    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMailTranslatableMailContent(MailTranslatable $mailTranslatable, string $locale, array $data, array $cachingKeys = []): string
    {
        return $this->getCachingMailContent($mailTranslatable->getMailTemplate(), $locale,[
            ...$this->getMailTranslateData($mailTranslatable,$locale),
            ...$data
        ],['title','body','headerImage']);
    }
    /**
     * @throws InvalidArgumentException
     */
    public function getCachingMailContent(string $mailTemplate, string $locale, array $data, array $cachingKeys = []):string
    {
        if(empty($mailTemplate)){
            return 'Please select a Mail Template';
        }
        $replaceableContent = [];

        foreach ($data as $key => $value) {
            $replaceableContent[$key] = '{{ ' . $key . ' }}';
        }
        $additionCachingKey = '';
        foreach ($cachingKeys as $cachingKey){
            if(array_key_exists($cachingKey,$data) && !empty($data[$cachingKey])){
                $additionCachingKey.=$cachingKey.'-not-empty';
            }else{
                $additionCachingKey.=$cachingKey.'-empty';
            }
        }
        $templateCacheKey = 'mail_translatable_' . hash('sha256', $mailTemplate . $locale.$additionCachingKey);
        $html = $this->cache->get($templateCacheKey, function () use ($replaceableContent, $mailTemplate, $locale) {

            $mjmlContent = $this->twig->render($mailTemplate . '.'
                . self::$EXTENSIONS, [...$replaceableContent,
                'locale' => $locale
            ]);
            return $this->mjmlAPIService->render($mjmlContent);
        });
        return str_replace(array_values($replaceableContent), array_values($data), $html);
    }

    private function getMailTranslateData(MailTranslatable $mailTranslatable, string $locale):array{
        $data = [];
        if(!empty($mailTranslatable->getTitle($locale))){
            $data['title'] = $mailTranslatable->getTitle($locale);
        }
        if(!empty($mailTranslatable->getBody($locale))){
            $data['body'] = $mailTranslatable->getBody($locale);
        }
        if (!empty($mailTranslatable->getHeaderImage($locale))) {
            $formats = $this->mediaManager->getFormatUrls([$mailTranslatable->getHeaderImage($locale)->getId()], $locale);
            $url = $formats[$mailTranslatable->getHeaderImage($locale)->getId()]['1080x.png'];
            $webspaceUrl = str_replace('/'.$locale,'',
                $this->webspaceManager->findUrlByResourceLocator($url, null,$locale));
            $data['headerImage']= $webspaceUrl ?? '';
        }
        return $data;
    }
}