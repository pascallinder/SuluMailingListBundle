<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;
use Qferrer\Mjml\Http\CurlApi;
use Qferrer\Mjml\Renderer\ApiRenderer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class MjmlAPIService
{
    private CurlApi $curlApi;
    private ApiRenderer $apiRenderer;

    public function __construct(
        #[Autowire('%sulu_mailing_list.mjml.app_id%')]
        private string $appId,
        #[Autowire('%sulu_mailing_list.mjml.secret_key%')]
        private string $secretKey
    )
    {
        $this->curlApi = new CurlApi($this->appId,$this->secretKey);
        $this->apiRenderer = new ApiRenderer($this->curlApi);
    }

    public function render(string $content):string{
        return $this->apiRenderer->render($content);
    }

}