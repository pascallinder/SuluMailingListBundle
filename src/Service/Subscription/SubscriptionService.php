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
        if(!isset($contactData['newsletters'])){
            return;
        }
        $this->handleNewsletters($contactData['newsletters'],[$contact],$locale);
    }

    /**
     * @param Newsletter[] $newsletters
     */
    public function handleNewsletters(array $newsletters, array $contacts, string $locale, bool $doubleOptEnabled = true): void
    {
        foreach ($contacts as $contact) {
            foreach ($newsletters as $newsletter){
                $existingSubscriptions = $this->newsletterSubscriptionRepository->findBy(['newsletter'=>$newsletter->getId(),
                    'contact'=>$contact->getId()]);
                if(empty($existingSubscriptions)){
                    $this->saveSubscription(new NewsletterSubscription($newsletter,$contact,$locale), $doubleOptEnabled);
                }
                else{
                    /** @var NewsletterSubscription $existingSubscription */
                    $existingSubscription = $existingSubscriptions[0];
                    $existingSubscription->getNewsletter()->setLocale($locale);
                    $existingSubscription->setSubscribed();
                    $this->saveSubscription($existingSubscription,$doubleOptEnabled);
                }
            }
        }

    }

    private function saveSubscription(NewsletterSubscription $newsletterSubscription , bool $doubleOptEnabled): void
    {
        if($doubleOptEnabled){
            $this->domainEventCollector->collect(new NewsletterSubscribedEvent($newsletterSubscription));
        }else{
            $this->newsletterSubscriptionRepository->save($newsletterSubscription);
            $newsletterSubscription->setIsConfirmed();
            $this->domainEventCollector->collect(new NewsletterConfirmedEvent($newsletterSubscription));
        }

        $this->newsletterSubscriptionRepository->save($newsletterSubscription);
        $this->newsletterSubscriptionRepository->flush();
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