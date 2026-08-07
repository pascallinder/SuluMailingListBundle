<?php

namespace Linderp\SuluMailingListBundle\Twig;

use Linderp\SuluMailingListBundle\Service\Mail\MailFontImageRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MailTextTwigExtension extends AbstractExtension
{
    public function __construct(private readonly MailFontImageRenderer $mailFontImageRenderer) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mail_text_item', [$this, 'renderTextItem']),
        ];
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function renderTextItem(array $item): array
    {
        return $this->mailFontImageRenderer->renderTextItem($item);
    }
}
