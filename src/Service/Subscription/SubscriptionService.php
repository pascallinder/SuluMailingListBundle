<?php

namespace Linderp\SuluMailingListBundle\Service\Subscription;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterConfirmedEvent;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterSubscribedEvent;
use Linderp\SuluMailingListBundle\Event\Newsletter\NewsletterUnsubscribedEvent;
use Linderp\SuluMailingListBundle\Repository\NewsletterSubscription\NewsletterSubscriptionRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;

readonly class SubscriptionService
{
    public function __construct(
        private NewsletterSubscriptionRepository $newsletterSubscriptionRepository,
        private DomainEventCollectorInterface    $domainEventCollector
    ){
    }
    public function handleSavedContact(ContactInterface $contact, array $contactData,string $locale): void
    {
        if(!array_key_exists('newsletter',$contactData)){
            return;
        }
        $this->handleNewsletter($contactData['newsletter'],$contact,$locale);
    }

    public function handleNewsletter(Newsletter $newsletter, ContactInterface $contact, string $locale): void
    {
        $existingSubscriptions = $this->newsletterSubscriptionRepository->findBy(['newsletter'=>$newsletter->getId(),
            'contact'=>$contact->getId()]);
        if(empty($existingSubscriptions)){
            $this->saveSubscription(new NewsletterSubscription($newsletter,$contact,$locale));
        }
        else{
            /** @var NewsletterSubscription $existingSubscription */
            $existingSubscription = $existingSubscriptions[0];
            if($existingSubscription->isUnsubscribed()){
                try {
                    $existingSubscription->setSubscribed();
                    $this->saveSubscription($existingSubscription);
                } catch (\Exception $e) {
                }
            }
        }
    }

    private function saveSubscription(NewsletterSubscription $newsletterSubscription): void
    {
        $this->domainEventCollector->collect(new NewsletterSubscribedEvent($newsletterSubscription));
        $this->newsletterSubscriptionRepository->save($newsletterSubscription);
    }

    public function unsubscribe(string $newsletterId, string $token): bool
    {

        $subscriptions = $this->newsletterSubscriptionRepository->findBy(['newsletter'=>$newsletterId, 'unsubscribeToken'=>$token,'isUnsubscribed'=>false]);
        if(count($subscriptions) === 0){
            return false;
        }
        /** @var NewsletterSubscription $subscription */
        $subscription = $subscriptions[0];
        $subscription->setUnsubscribed();
        $subscription->getNewsletter()->setLocale($subscription->getLocale());
        $this->domainEventCollector->collect(new NewsletterUnsubscribedEvent($subscription));
        $this->newsletterSubscriptionRepository->save($subscription);
        $this->newsletterSubscriptionRepository->flush();
        return true;
    }

    public function confirmDoubleOpt(string $newsletterId, string $token):bool
    {
        $subscriptions = $this->newsletterSubscriptionRepository->findBy(['newsletter'=>$newsletterId, 'confirmationToken'=>$token,'isUnsubscribed'=>false]);
        if(count($subscriptions) === 0){
            return false;
        }
        /** @var NewsletterSubscription $subscription */
        $subscription = $subscriptions[0];
        $subscription->setIsConfirmed();
        $subscription->getNewsletter()->setLocale($subscription->getLocale());
        $this->domainEventCollector->collect(new NewsletterConfirmedEvent($subscription));
        $this->newsletterSubscriptionRepository->save($subscription);
        $this->newsletterSubscriptionRepository->flush();
        return true;
    }
}