<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Finder\Finder;
#[AsAlias(id: 'sulu_mailing_list.mail_template_select', public: true)]
readonly class MailTemplateSelect
{
    private string $projectDir;
    public function __construct( string $projectDir)
    {
        // This resolves to root/templates/mails/newsletter
        $this->projectDir = $projectDir . '/templates';
    }
    public function getValues(string $relativeTemplatePath): array
    {
        $finder = new Finder();
        $finder->files()->in($this->projectDir.'/'.ltrim($relativeTemplatePath,'/'));
        /** @var \SplFileInfo[] $files */
        $files = [];
        foreach ($finder as $file) {
            $files[] = $file;
        }
        $mapped= (array_map(fn(\SplFileInfo $file)=>['name'=>$this->stripExtensions($file->getRealPath()),
            'title'=>explode('.',$file->getFilename())[0]],$files));
        return array_values(array_reduce($mapped, function ($carry, $item) {
            static $names = [];
            if (!in_array($item['name'], $names, true)) {
                $names[] = $item['name'];
                $carry[] = $item;
            }
            return $carry;
        }, []));
    }
    private function stripExtensions(string $path): string {
        $serverPath =substr($path,strpos($path, '/mails/') + 1);
        $parts = pathinfo($serverPath);
        $dirname = $parts['dirname'];

        // Get filename before first dot
        $filename = explode('.', $parts['filename'])[0];
        return $dirname . '/' . $filename;
    }
}