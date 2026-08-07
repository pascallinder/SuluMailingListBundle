<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\Service\Mail;

use Qferrer\Mjml\Http\Curl;
use Qferrer\Mjml\Http\CurlInterface;
use Qferrer\Mjml\Http\CurlResponseInterface;

final readonly class BoundedCurl implements CurlInterface
{
    private const CONNECT_TIMEOUT_SECONDS = 3;
    private const REQUEST_TIMEOUT_SECONDS = 10;

    public function __construct(private CurlInterface $curl = new Curl()) {}

    /**
     * @param array<int, mixed> $options
     */
    public function request(string $url, array $options = []): CurlResponseInterface
    {
        // qferr/mjml-php sets CURLOPT_TIMEOUT to 0. Always override that
        // unbounded value so email rendering cannot freeze an HTTP request.
        $options[CURLOPT_CONNECTTIMEOUT] = self::CONNECT_TIMEOUT_SECONDS;
        $options[CURLOPT_TIMEOUT] = self::REQUEST_TIMEOUT_SECONDS;

        return $this->curl->request($url, $options);
    }
}
