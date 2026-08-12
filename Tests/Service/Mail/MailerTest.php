<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Service\Mail;

use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Linderp\SuluMailingListBundle\Service\Mail\Mailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

final class MailerTest extends TestCase
{
    public function testItPreparesTestMailWithoutSubscription(): void
    {
        $newsletterMail = (new NewsletterMail())->setLocale('de');
        $newsletterMail->setSenderMail('newsletter@example.com');
        $newsletterMail->setSubject('Aktuelles von Refashion');

        $contentProvider = $this->createMock(MailContentProvider::class);
        $contentProvider->expects(self::once())
            ->method('getMailTranslatableMailContent')
            ->with($newsletterMail, 'de', [
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
                'unsubscribeUrl' => '#',
            ])
            ->willReturn('<p>Test content</p>');

        $mailer = new Mailer($this->createMock(MailerInterface::class), $contentProvider);
        $email = $mailer->prepareTestMail($newsletterMail, 'preview@example.com', 'de');

        self::assertSame('newsletter@example.com', $email->getFrom()[0]->getAddress());
        self::assertSame('preview@example.com', $email->getTo()[0]->getAddress());
        self::assertSame('Aktuelles von Refashion', $email->getSubject());
        self::assertSame('<p>Test content</p>', $email->getHtmlBody());
    }
}
