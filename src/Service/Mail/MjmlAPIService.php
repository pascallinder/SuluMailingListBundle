<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;
use Qferrer\Mjml\Http\CurlApi;
use Qferrer\Mjml\Renderer\ApiRenderer;

readonly class MjmlAPIService
{
    private string $apiId;
    private string $secretKey;
    private CurlApi $curlApi;
    private ApiRenderer $apiRenderer;

    public function __construct()
    {
        $this->apiId = "682ca3e7-767e-43f8-9c16-3747dcbc2264";
        $this->secretKey = '07aba5e5-474d-4eb8-a0e4-155565248aab';
        $this->curlApi = new CurlApi($this->apiId,$this->secretKey);
        $this->apiRenderer = new ApiRenderer($this->curlApi);
    }

    public function render(string $content):string{
        return $this->apiRenderer->render($content);
    }

}