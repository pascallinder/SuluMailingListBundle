<?php

namespace Linderp\SuluMailingListBundle\Tests\Entity;

use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOpt;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use PHPUnit\Framework\TestCase;

class MailTemplateSelectionTest extends TestCase
{
    public function testFooterIsRequired(): void
    {
        $mail = new NewsletterMail();

        $this->expectException(\InvalidArgumentException::class);
        $mail->setFooter(null);
    }

    public function testNewsletterMailCopyKeepsHeaderAndFooter(): void
    {
        $header = new MailTemplateHeader();
        $footer = new MailTemplateFooter();
        $mail = $this->configureMail(new NewsletterMail());
        $mail->setHeader($header);
        $mail->setFooter($footer);

        $copy = $mail->copy();

        self::assertSame($header, $copy->getHeader());
        self::assertSame($footer, $copy->getFooter());
    }

    public function testDoubleOptCopyKeepsHeaderAndFooter(): void
    {
        $header = new MailTemplateHeader();
        $footer = new MailTemplateFooter();
        $mail = $this->configureMail(new NewsletterDoubleOpt());
        $mail->setHeader($header);
        $mail->setFooter($footer);

        $copy = $mail->copy();

        self::assertSame($header, $copy->getHeader());
        self::assertSame($footer, $copy->getFooter());
    }

    /**
     * @template T of NewsletterMail|NewsletterDoubleOpt
     * @param T $mail
     * @return T
     */
    private function configureMail(NewsletterMail|NewsletterDoubleOpt $mail): NewsletterMail|NewsletterDoubleOpt
    {
        $mail->setLocale('de');
        $mail->setSenderMail('no-reply@example.com');
        $mail->setContext('default');
        $mail->setContextVars([]);
        $mail->setSubject('Test');
        $mail->setContent([]);

        return $mail;
    }
}
