<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Mail\Font;

use Linderp\SuluMailingListBundle\Mail\Font\MailFontConfiguration;
use PHPUnit\Framework\TestCase;

final class MailFontConfigurationTest extends TestCase
{
    public function testItConfiguresMailRenderingPropertiesFluently(): void
    {
        $configuration = new MailFontConfiguration('/fonts.css', 'Momo', 'Momo, sans-serif');

        self::assertSame($configuration, $configuration->setDefaultFont(true));
        self::assertSame($configuration, $configuration->setWebFont(true));
        self::assertSame($configuration, $configuration->setStrokeWidth(1.5));
        self::assertSame('/fonts.css', $configuration->getCssUrl());
        self::assertSame('Momo', $configuration->getName());
        self::assertSame('Momo, sans-serif', $configuration->getFontFamily());
        self::assertTrue($configuration->isDefaultFont());
        self::assertTrue($configuration->isWebFont());
        self::assertSame(1.5, $configuration->getStrokeWidth());
    }

    public function testItClampsNegativeStrokeWidthsToZero(): void
    {
        $configuration = new MailFontConfiguration('/fonts.css', 'Momo', 'Momo, sans-serif');

        $configuration->setStrokeWidth(-0.5);

        self::assertSame(0.0, $configuration->getStrokeWidth());
    }
}
