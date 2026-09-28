<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Preview\Newsletter\Mail;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\Newsletter\Mail\NewsletterMailPreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use PHPUnit\Framework\TestCase;

final class NewsletterMailPreviewObjectProviderTest extends TestCase
{
    public function testItResolvesSelectedHeaderAndFooterForPreview(): void
    {
        $header = new MailTemplateHeader();
        $footer = new MailTemplateFooter();
        $headerRepository = $this->createMock(ObjectRepository::class);
        $headerRepository->method('find')->with(11)->willReturn($header);
        $footerRepository = $this->createMock(ObjectRepository::class);
        $footerRepository->method('find')->with(22)->willReturn($footer);

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getRepository')->willReturnCallback(
            static fn(string $class): ObjectRepository => match ($class) {
                MailTemplateHeader::class => $headerRepository,
                MailTemplateFooter::class => $footerRepository,
                default => throw new \InvalidArgumentException('Unexpected repository class.'),
            }
        );

        $contextType = $this->createMock(MailContextTypeInterface::class);
        $contextType->method('getConfiguration')->willReturn(
            new MailContextTypeConfiguration('Default', 'default.xml', 'default')
        );
        $provider = new NewsletterMailPreviewObjectProvider(
            new MailContextTypesPool([$contextType]),
            $managerRegistry,
            $this->createMock(NewsletterMailRepository::class),
            $this->createMock(NewsletterMailTranslationRepository::class),
        );
        $mail = new NewsletterMail();
        $mail->setLocale('en');

        $provider->setValues($mail, 'en', [
            'context' => 'default',
            'content_default' => [],
            'backgroundColor' => '#FAFAFA',
            'header' => 11,
            'footer' => 22,
        ]);

        self::assertSame($header, $mail->getHeader());
        self::assertSame($footer, $mail->getFooter());
    }
}
