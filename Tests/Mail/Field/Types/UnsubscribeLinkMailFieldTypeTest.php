<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Mail\Field\Types;

use Linderp\SuluMailingListBundle\Mail\Field\Types\UnsubscribeLinkMailFieldType;
use Linderp\SuluMailingListBundle\Mail\Resource\Types\MailTemplateFooterResource;
use PHPUnit\Framework\TestCase;

final class UnsubscribeLinkMailFieldTypeTest extends TestCase
{
    public function testItIsOnlyAvailableForFooterTemplates(): void
    {
        $configuration = (new UnsubscribeLinkMailFieldType())->getConfiguration();

        self::assertSame('unsubscribe-link', $configuration->getKey());
        self::assertSame([MailTemplateFooterResource::class], $configuration->getAcceptedResources());
    }

    public function testItKeepsTheCustomLabel(): void
    {
        $fieldType = new UnsubscribeLinkMailFieldType();
        $item = ['label' => 'Stop receiving these emails'];

        self::assertSame($item, $fieldType->build($item, 'en'));
    }
}
