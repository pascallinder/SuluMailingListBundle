<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Symfony\Component\Routing\Attribute\Route;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\BaseController;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterSubscribedEvent;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterUnsubscribedEvent;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterSubscription\NewsletterSubscriptionRepository;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionService;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\ContactBundle\Entity\ContactRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterSubscriptionController extends BaseController
{

    public function __construct(
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly NewsletterSubscriptionRepository $newsletterSubscriptionRepository,
        private readonly NewsletterRepository $newsletterRepository,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly SubscriptionService $subscriptionService,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory
    ){
    }

    #[Route(path: '/admin/api/newsletters-subscriptions/{id}', name: 'app.get_newsletter_subscription', methods: ['GET'])]
    public function getAction(int $id, Request $request): Response
    {
        return $this->handleGetByIdRequest($id,$request);
    }
    #[Route(path: '/admin/api/newsletters-subscriptions', name: 'app.post_newsletter_subscription', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        $data = $request->toArray();
        foreach ( $data['newsletters'] as $newsletterId){
            $this->subscriptionService->handleNewsletter(  $this->newsletterRepository->find($newsletterId),
                $this->contactRepository->find($data['contact']),
                $data['locale']
            );

        }
        return new Response(json_encode(['success' => true]));
    }

    #[Route(path: '/admin/api/newsletters-subscriptions/{id}', name: 'app.post_newsletter_subscription_trigger', methods: ['POST'])]
    public function postTriggerAction(int $id, Request $request): Response
    {
        return $this->handlePostTriggerRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters-subscriptions/{id}', name: 'app.delete_newsletter_subscription', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        return $this->handleDeleteRequest($id);
    }
    #[Route(path: '/admin/api/newsletters-subscriptions', name: 'app.get_newsletter_subscriptions_list', methods: ['GET'])]
    public function getSubscriptionListAction(Request $request): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            NewsletterSubscription::RESOURCE_KEY,
            [],
            [],
        );

        return $this->json($listRepresentation->toArray());
    }
    protected function getDataForEntity($entity, Request $request): array
    {
        return ["test" => "test"];
    }

    protected function mapDataToEntity(array $data, $entity, Request $request): void{}


    protected function load(int $id, Request $request): ?NewsletterSubscription
    {
        /** @var ?NewsletterSubscription $newsletterSubscription */
        $newsletterSubscription =  $this->newsletterSubscriptionRepository->find($id);
        return $newsletterSubscription;
    }
    protected function create(Request $request): void{}

    protected function save($entity): void{
        $this->newsletterSubscriptionRepository->save($entity);
        $this->newsletterSubscriptionRepository->flush();
    }

    protected function remove(int $id): void
    {}

    /**
     * @param NewsletterSubscription $entity
     * @throws \Exception
     */
    protected function triggerSwitch(Request $request, string $action, $entity){
        $entity->getNewsletter()->setLocale($request->getLocale());
        switch ($action) {
            case 'unsubscribe':
                if(!$entity->isUnsubscribed()){
                    $entity->setUnsubscribed();
                    $this->domainEventCollector->collect(new NewsletterUnsubscribedEvent($entity));
                }
                break;
            case 'subscribe':
                if($entity->isUnsubscribed()) {
                    $entity->setSubscribed();
                    $this->domainEventCollector->collect(new NewsletterSubscribedEvent($entity));
                }
                break;
        }
    }
}