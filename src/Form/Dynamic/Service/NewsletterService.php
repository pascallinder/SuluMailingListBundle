<?php

namespace Linderp\SuluMailingListBundle\Form\Dynamic\Service;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\RequestStack;
#[AsAlias(id: 'sulu_mailing_list.newsletter_service', public: true)]
readonly class NewsletterService
{
    public function __construct(private NewsletterRepository $newsletterRepository,
    private RequestStack                                     $requestStack)
    {}

    public function getValues(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        return array_map(fn(Newsletter $category)=> ['name'=> $category->getId(), 'title'=>$category->getTitle()],
            $this->newsletterRepository->findAllLocalized($request->getLocale()));
    }
}