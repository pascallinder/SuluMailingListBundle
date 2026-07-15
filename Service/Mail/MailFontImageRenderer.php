<?php

namespace Linderp\SuluMailingListBundle\Service\Mail;

use Linderp\SuluMailingListBundle\Mail\Font\MailFontConfiguration;
use Linderp\SuluMailingListBundle\Mail\Font\MailFontPool;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

class MailFontImageRenderer
{
    private const RENDER_SCALE = 2;
    private const MAX_IMAGE_WIDTH = 600;
    private const IMAGE_PADDING = 4;
    private const PADDING_SIDES = 2;
    private const LINE_HEIGHT_PADDING = 4;

    public function __construct(
        #[Autowire('%sulu_mailing_list.mjml.font_images_path%')]
        private readonly string $imagePath,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        private readonly RequestStack $requestStack,
        private readonly MailFontPool $mailFontPool,
    ) {
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function renderTextItem(array $item): array
    {
        if (isset($item['image'], $item['imageWidth'])) {
            return $item;
        }

        $item['align'] = $this->getTextAlignment($item);

        $fontFamily = $item['fontFamily'] ?? $this->mailFontPool->getDefaultValue();
        $font = $this->mailFontPool->getAll()[$fontFamily] ?? null;
        $fontConfiguration = $font?->getConfiguration();
        if (!$fontConfiguration?->isWebFont()) {
            return $item;
        }

        $image = $this->render($item, $fontConfiguration);
        if ($image === null) {
            return $item;
        }

        return [
            ...$item,
            'image' => $image['url'],
            'imageWidth' => $image['width'],
        ];
    }

    /**
     * @param array<string, mixed> $item
     */
    private function getTextAlignment(array $item): string
    {
        $alignment = strtolower((string) ($item['align'] ?? ''));
        if (in_array($alignment, ['left', 'center', 'right'], true)) {
            return $alignment;
        }

        $value = (string) ($item['value'] ?? '');
        if (preg_match('/text-align\s*:\s*(left|center|right)\b/i', $value, $matches)) {
            return strtolower($matches[1]);
        }
        if (preg_match('/\balign\s*=\s*["\'](left|center|right)["\']/i', $value, $matches)) {
            return strtolower($matches[1]);
        }

        return 'left';
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array{url: string, width: int}|null
     */
    public function render(array $item, MailFontConfiguration $font): ?array
    {
        $publicDirectory = rtrim($this->getPublicDirectory(), '/');
        $imagePath = rtrim($this->imagePath, '/');
        if (!class_exists(\Imagick::class) || $imagePath === '' || !is_dir($publicDirectory) ||
            !str_starts_with($imagePath, $publicDirectory . '/')) {
            return null;
        }

        $fontSize = max(1, (int) ($item['fontSize'] ?? 16));
        $lineHeight = max(1.0, (float) ($item['fontLineHeight'] ?? 1));
        $text = $this->toPlainText((string) ($item['value'] ?? ''));
        if ($text === '') {
            return null;
        }

        $hash = hash('sha256', json_encode([
            'imagick-v1',
            $text,
            $font->getCssUrl(),
            $font->getFontFamily(),
            $fontSize,
            $lineHeight,
            $item['color'] ?? null,
            $item['align'] ?? null,
        ], JSON_THROW_ON_ERROR));
        $filename = $hash . '.png';
        $filePath = $imagePath . '/' . $filename;

        $width = null;
        if (!is_file($filePath)) {
            $width = $this->createImage($filePath, $text, $font, $fontSize, $lineHeight, $item);
            if ($width === null) {
                return null;
            }
        }

        if ($width === null) {
            try {
                $image = new \Imagick($filePath);
                $width = (int) ceil($image->getImageWidth() / self::RENDER_SCALE);
                $image->clear();
            } catch (\Throwable) {
                return null;
            }
        }

        return [
            'url' => $this->getPublicUrl($filename),
            'width' => max(1, $width),
        ];
    }

    /** @param array<string, mixed> $item
     * @throws \ImagickDrawException
     * @throws \ImagickException
     */
    private function createImage(
        string $filePath,
        string $text,
        MailFontConfiguration $font,
        int $fontSize,
        float $lineHeight,
        array $item,
    ): ?int {
        if (!is_dir($this->imagePath) && !mkdir($this->imagePath, 0775, true) && !is_dir($this->imagePath)) {
            return null;
        }

        $fontFilePath = $this->getFontFilePath($font);
        if ($fontFilePath === null) {
            return null;
        }

        $scale = self::RENDER_SCALE;
        $fontSize *= $scale;
        $maxWidth = self::MAX_IMAGE_WIDTH * $scale;
        $canvas = new \Imagick();
        $draw = new \ImagickDraw();
        $draw->setFont($fontFilePath);
        $draw->setFontSize($fontSize);
        $draw->setFillColor($this->getTextColor($item['color'] ?? null));
        $draw->setTextAntialias(true);

        try {
            $lines = $this->wrapText($canvas, $draw, $text, $maxWidth);
            $lineWidths = array_map(
                fn (string $line): int => $this->getTextWidth($canvas, $draw, $line),
                $lines
            );
            $lineHeightInPixels = max(
                (int) round($fontSize * $lineHeight),
                $fontSize + self::LINE_HEIGHT_PADDING,
            );
            $padding = self::IMAGE_PADDING * $scale;
            $width = min($maxWidth, max($lineWidths)) + (self::PADDING_SIDES * $padding);
            $height = (count($lines) * $lineHeightInPixels) + (self::PADDING_SIDES * $padding);

            $canvas->newImage($width, $height, new \ImagickPixel('transparent'));
            $canvas->setImageFormat('png');
            $canvas->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
            $alignment = $item['align'] ?? 'left';
            foreach ($lines as $index => $line) {
                $lineWidth = $lineWidths[$index];
                $x = match ($alignment) {
                    'center' => (int) round(($width - $lineWidth) / self::PADDING_SIDES),
                    'right' => $width - $lineWidth - $padding,
                    default => $padding,
                };
                $canvas->annotateImage($draw, $x, $padding + $fontSize + ($index * $lineHeightInPixels), 0, $line);
            }

            if (!$canvas->writeImage($filePath)) {
                return null;
            }

            return (int) ceil($width / $scale);
        } catch (\Throwable) {
            @unlink($filePath);
            return null;
        } finally {
            $canvas->clear();
            $draw->clear();
        }
    }

    /** @return list<string> */
    private function wrapText(\Imagick $canvas, \ImagickDraw $draw, string $text, int $maxWidth): array
    {
        $lines = [];
        foreach (preg_split('/\R/u', $text) ?: [$text] as $line) {
            $words = preg_split('/\s+/u', trim($line)) ?: [''];
            $current = '';
            foreach ($words as $word) {
                $candidate = $current === '' ? $word : $current . ' ' . $word;
                if ($current !== '' && $this->getTextWidth($canvas, $draw, $candidate) > $maxWidth) {
                    $lines[] = $current;
                    $current = $word;
                } else {
                    $current = $candidate;
                }
            }
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }

    private function getTextWidth(\Imagick $canvas, \ImagickDraw $draw, string $text): int
    {
        $metrics = $canvas->queryFontMetrics($draw, $text);

        return (int) ceil($metrics['textWidth'] ?? 0);
    }

    private function getFontFilePath(MailFontConfiguration $font): ?string
    {
        $cssUrl = $font->getCssUrl();
        $fontPath = sys_get_temp_dir() . '/mail-font-' . hash('sha256', $cssUrl);
        if (is_file($fontPath)) {
            return $fontPath;
        }

        $cssPath = $this->getPublicFilePath($cssUrl);
        if ($cssPath === null) {
            return null;
        }
        $css = file_get_contents($cssPath);
        if ($css === false) {
            return null;
        }

        preg_match_all('/url\(\s*[\'\"]?([^\'\")]+)[\'\"]?\s*\)/i', $css, $matches);
        foreach ($matches[1] ?? [] as $candidateUrl) {
            if (str_starts_with($candidateUrl, 'data:')) {
                continue;
            }

            $fontUrl = $this->resolveFontUrl($cssUrl, $candidateUrl);
            $extension = strtolower(pathinfo(parse_url($fontUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            if (!in_array($extension, ['woff2', 'woff', 'ttf', 'otf'], true)) {
                continue;
            }

            $localFontPath = $this->getPublicFilePath($fontUrl);
            if ($localFontPath === null) {
                continue;
            }
            $fontData = file_get_contents($localFontPath);
            if ($fontData !== false && file_put_contents($fontPath, $fontData) !== false) {
                return $fontPath;
            }
        }

        return null;
    }

    private function getPublicFilePath(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $publicDirectory = realpath($this->getPublicDirectory());
        if (!is_string($path) || !str_starts_with($path, '/') || $publicDirectory === false) {
            return null;
        }

        $filePath = realpath($publicDirectory . '/' . ltrim(rawurldecode($path), '/'));
        if ($filePath === false || !str_starts_with($filePath, $publicDirectory . DIRECTORY_SEPARATOR) || !is_file($filePath)) {
            return null;
        }

        return $filePath;
    }

    private function resolveFontUrl(string $cssUrl, string $fontUrl): string
    {
        if (preg_match('/^https?:\/\//i', $fontUrl)) {
            return $fontUrl;
        }
        $cssParts = parse_url($cssUrl);
        if (str_starts_with($fontUrl, '/') && isset($cssParts['scheme'], $cssParts['host'])) {
            return $cssParts['scheme'] . '://' . $cssParts['host'] . $fontUrl;
        }

        return rtrim(substr($cssUrl, 0, strrpos($cssUrl, '/') ?: 0), '/') . '/' . ltrim($fontUrl, '/');
    }

    private function getTextColor(mixed $color): string
    {
        return is_string($color) && preg_match('/^#[0-9a-f]{3,8}$/i', $color) ? $color : '#000000';
    }

    private function getPublicDirectory(): string
    {
        return rtrim($this->projectDir, '/') . '/public';
    }

    private function getPublicUrl(string $filename): string
    {
        $relativePath = str_replace($this->getPublicDirectory(), '', rtrim($this->imagePath, '/'));
        $relativeUrl = '/' . trim($relativePath, '/') . '/' . $filename;
        $request = $this->requestStack->getCurrentRequest();

        return $request ? $request->getSchemeAndHttpHost() . $relativeUrl : $relativeUrl;
    }

    private function toPlainText(string $html): string
    {
        $html = preg_replace('/<br\s*\/?\s*>/i', "\n", $html) ?? $html;
        $html = preg_replace('/<\/(p|div|li|h[1-6])\s*>/i', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[ \t]+/u', ' ', $text) ?? $text);
    }
}
