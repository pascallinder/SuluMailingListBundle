<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Tests\Service\Mail;

use Linderp\SuluMailingListBundle\Service\Mail\BoundedCurl;
use PHPUnit\Framework\TestCase;
use Qferrer\Mjml\Http\CurlInterface;
use Qferrer\Mjml\Http\CurlResponseInterface;

final class BoundedCurlTest extends TestCase
{
    public function testItForcesBoundedConnectionAndRequestTimeouts(): void
    {
        $response = $this->createMock(CurlResponseInterface::class);
        $curl = $this->createMock(CurlInterface::class);
        $curl->expects(self::once())
            ->method('request')
            ->with('https://mjml.example/render', self::callback(static function (array $options): bool {
                return 3 === $options[CURLOPT_CONNECTTIMEOUT]
                    && 10 === $options[CURLOPT_TIMEOUT]
                    && 'payload' === $options[CURLOPT_POSTFIELDS];
            }))
            ->willReturn($response);

        $result = (new BoundedCurl($curl))->request('https://mjml.example/render', [
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POSTFIELDS => 'payload',
        ]);

        self::assertSame($response, $result);
    }
}
