<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Standard ISO/IEC 15417 Code 128 patterns (values 0..106).
     * Each digit represents module widths alternating bar, space, bar, space, bar, space (Stop ends with bar).
     */
    private const PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0..9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10..19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20..29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30..39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40..49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50..59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60..69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70..79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80..89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90..99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',                               // 100..106
    ];

    /**
     * Encode printable ASCII string using Code 128 Set B.
     * Returns an array of [widthModules, isBar] tuples including quiet zones.
     *
     * @return array<int, array{width: int, bar: bool}>
     */
    public function encodeCode128B(string $text): array
    {
        $cleanText = preg_replace('/[^\x20-\x7E]/', '', $text) ?? '';
        if ($cleanText === '') {
            $cleanText = 'SIPKESMA';
        }

        // Start B = 104
        $codes = [104];
        $checksum = 104;

        $len = strlen($cleanText);
        for ($i = 0; $i < $len; $i++) {
            $val = ord($cleanText[$i]) - 32;
            $codes[] = $val;
            $checksum += ($i + 1) * $val;
        }

        $codes[] = $checksum % 103;
        $codes[] = 106; // Stop code

        $modules = [];
        // Left quiet zone (10 modules white)
        $modules[] = ['width' => 10, 'bar' => false];

        foreach ($codes as $codeIndex) {
            $pattern = self::PATTERNS[$codeIndex];
            $pLen = strlen($pattern);
            for ($j = 0; $j < $pLen; $j++) {
                $modules[] = [
                    'width' => (int) $pattern[$j],
                    'bar' => ($j % 2 === 0),
                ];
            }
        }

        // Right quiet zone (10 modules white)
        $modules[] = ['width' => 10, 'bar' => false];

        return $modules;
    }

    /**
     * Generate crisp scannable SVG 1D Code 128B barcode.
     */
    public function toSvg(string $text, int $height = 56, int $moduleWidth = 2, string $barColor = '#0f172a'): string
    {
        $modules = $this->encodeCode128B($text);
        $totalModules = array_sum(array_column($modules, 'width'));
        $totalWidth = $totalModules * $moduleWidth;

        $rects = '';
        $x = 0;
        foreach ($modules as $seg) {
            $w = $seg['width'] * $moduleWidth;
            if ($seg['bar']) {
                $rects .= "<rect x=\"{$x}\" y=\"0\" width=\"{$w}\" height=\"{$height}\" fill=\"{$barColor}\"/>";
            }
            $x += $w;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $totalWidth . ' ' . $height . '" width="' . $totalWidth . '" height="' . $height . '" role="img" aria-label="Barcode ' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '" shape-rendering="crispEdges">'
            . '<rect x="0" y="0" width="' . $totalWidth . '" height="' . $height . '" fill="#ffffff"/>'
            . $rects
            . '</svg>';
    }

    /**
     * Generate base64 SVG Data URI for <img> tags (including DomPDF compatibility).
     */
    public function toDataUri(string $text, int $height = 56, int $moduleWidth = 2, string $barColor = '#0f172a'): string
    {
        $svg = $this->toSvg($text, $height, $moduleWidth, $barColor);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate pure HTML table barcode bars (fallback/native rendering in DomPDF).
     */
    public function toHtmlTable(string $text, int $height = 38, int $moduleWidth = 1, string $barColor = '#0f172a'): string
    {
        $modules = $this->encodeCode128B($text);
        $cells = '';
        foreach ($modules as $seg) {
            $w = $seg['width'] * $moduleWidth;
            $bg = $seg['bar'] ? $barColor : '#ffffff';
            $cells .= "<td style=\"width:{$w}px;height:{$height}px;background-color:{$bg};padding:0;margin:0;border:none;font-size:1px;line-height:1px;\"></td>";
        }

        return '<table cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto;background-color:#ffffff;"><tr>' . $cells . '</tr></table>';
    }
}

