<?php

namespace Linderp\SuluMailingListBundle\Subscriber;

use Linderp\SuluFormSaveContactBundle\Event\DynamicFormSavedContactEvent;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ContactSavedSubscriber implements EventSubscriberInterface
{
    public function __construct(private SubscriptionService $subscriptionService){

    }
    public static function getSubscribedEvents(): array
    {
        return [
            DynamicFormSavedContactEvent::class => "contactSavedThroughDynamicForm",
        ];
    }
    public function contactSavedThroughDynamicForm(DynamicFormSavedContactEvent $event){
        $this->subscriptionService->handleSavedContact($event->getContact(),$event->getContactData(), $event->getLocale());
    }
}