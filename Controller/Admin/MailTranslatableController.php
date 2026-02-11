<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;

use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template T of object
 * @extends LocaleController<T>
 */
abstract class MailTranslatableController extends LocaleController
{
    /**
     * @param LocaleRepositoryUtil<T> $localeRepositoryUtil
     */
    public function __construct(
        protected readonly MailContextTypesPool $mailContextTypes,
        protected readonly string $noReplyEmail,
        protected readonly MailContentProvider $mailContentProvider,
        LocaleRepositoryUtil $localeRepositoryUtil)
    {
        parent::__construct($localeRepositoryUtil);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function getDataForMailTranslatable(MailTranslatable $mailTranslatable, array $data): array
    {
        $data = [...$data,
            'subject' => $mailTranslatable->getSubject(),
            'senderMail'=>$mailTranslatable->getSenderMail(),
            'context'=>$mailTranslatable->getContext(),
            'content_'.$mailTranslatable->getContext() => $mailTranslatable->getContent() ?? []
        ];
        foreach ($mailTranslatable->getContextVars() ?? [] as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToMailTranslatable(MailTranslatable $mailTranslatable, array $data): void{
        $mailTranslatable->setSenderMail($data['senderMail'] ?? $this->noReplyEmail);
        $mailTranslatable->setContext($data['context']);
        $mailTranslatable->setContent($data['content_'.$data['context']]);
        $contextType= $this->mailContextTypes->get($mailTranslatable->getContext());
        $contextVars= array_reduce($contextType->getConfiguration()->getContextVarsKeys(),
            fn(array $carry, string $key): array => [...$carry, $key => $data[$key] ?? null], []);
        $mailTranslatable->setContextVars($contextVars);
    }

    /**
     * @throws InvalidArgumentException
     */
    /**
     * @param array<string, mixed> $additionalData
     */
    protected function getIndexResponse(MailTranslatable $mail, array $additionalData = []): Response
    {
        return new Response('<!-- CONTENT-REPLACER -->'.$this->mailContentProvider->getMailTranslatableMailContent($mail,$mail->getLocale(),[
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'unsubscribeUrl' => 'https://google.ch',
                    ...$additionalData]
            ).'<!-- CONTENT-REPLACER -->');
    }
}
