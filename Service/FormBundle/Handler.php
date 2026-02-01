<?php

namespace Linderp\SuluMailingListBundle\Service\FormBundle;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\FormBundle\Configuration\FormConfigurationInterface;
use Sulu\Bundle\FormBundle\Form\HandlerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;

readonly class Handler implements HandlerInterface
{
    private Filesystem $filesystem;
    private string $templatesPath;
    public function __construct(private HandlerInterface    $inner,
                                string                      $projectDir,
                                private MailContentProvider $mailContentProvider){
        $this->filesystem = new Filesystem();
        $this->templatesPath = $projectDir . '/templates';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(FormInterface $form, FormConfigurationInterface $configuration): bool
    {

        $config = $configuration->getWebsiteMailConfiguration();
        if($config != null){
            $fullPath = $this->templatesPath . '/' . ltrim($config->getTemplate(), '/');
            if (!$this->filesystem->exists($fullPath)) {
                $result = $this->mailContentProvider->getCachingMailContent(explode(".",$config->getTemplate())[0],$configuration->getLocale(),[
                    'firstName' => "{{ formEntity.fields|filter(field => field.key == 'firstName')|first.value|default('') }}",
                    'body' => "{{ formEntity.mailText|default('')|raw }}"
                ]);
                $this->filesystem->dumpFile($fullPath, $result);
            }
        }
        return $this->inner->handle($form, $configuration);
    }
}