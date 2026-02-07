<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;

use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

abstract class MailTranslatableController extends LocaleController
{
    public function __construct(
        protected readonly MailContextTypesPool $mailContextTypes,
        protected readonly string $noReplyEmail,
        protected readonly MailContentProvider $mailContentProvider,
        LocaleRepositoryUtil $localeRepositoryUtil)
    {
        parent::__construct($localeRepositoryUtil);
    }

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

    protected function mapDataToMailTranslatable(MailTranslatable $mailTranslatable, array $data): void{
        $mailTranslatable->setSenderMail($data['senderMail'] ?? $this->noReplyEmail);
        $mailTranslatable->setContext($data['context']);
        $mailTranslatable->setContent($data['content_'.$data['context']]);
        $contextType= $this->mailContextTypes->get($mailTranslatable->getContext());
        $contextVars= array_reduce($contextType->getConfiguration()->getContextVarsKeys(),
            fn($carry, $key) => [...$carry, $key => $data[$key]],[]);
        $mailTranslatable->setContextVars($contextVars);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getIndexResponse(MailTranslatable $mail): Response
    {
        return new Response('<!-- CONTENT-REPLACER -->'.$this->mailContentProvider->getMailTranslatableMailContent($mail,$mail->getLocale(),[
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'unsubscribeUrl' => 'https://google.ch']
            ).'<!-- CONTENT-REPLACER -->');
    }
}