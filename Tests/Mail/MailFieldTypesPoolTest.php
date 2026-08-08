<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Mail;

use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeConfiguration;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypeInterface;
use Linderp\SuluMailingListBundle\Mail\Field\MailFieldTypesPool;
use PHPUnit\Framework\TestCase;

final class MailFieldTypesPoolTest extends TestCase
{
    public function testItIndexesHandlersByConfigurationKey(): void
    {
        $text = $this->createType('Text', 'text', 20);
        $button = $this->createType('Button', 'button', 10);
        $pool = new MailFieldTypesPool([$text, $button]);

        self::assertSame($text, $pool->get('text'));
        self::assertSame([$text, $button], $pool->getAll());
    }

    public function testItSortsByPriorityAndThenTitle(): void
    {
        $text = $this->createType('Text', 'text', 20);
        $social = $this->createType('Social', 'social', 10);
        $button = $this->createType('Button', 'button', 10);
        $pool = new MailFieldTypesPool([$text, $social, $button]);

        self::assertSame([$button, $social, $text], $pool->getAllSorted());
        self::assertSame([$text, $social, $button], $pool->getAll());
    }

    private function createType(string $title, string $key, int $priority): MailFieldTypeInterface
    {
        $configuration = (new MailFieldTypeConfiguration($title, $key . '.xml', $key))
            ->setPriority($priority);
        $type = $this->createMock(MailFieldTypeInterface::class);
        $type->method('getConfiguration')->willReturn($configuration);

        return $type;
    }
}
