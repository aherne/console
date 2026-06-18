<?php

namespace Lucinda\Console\Rendering;

use Lucinda\Console\Exception;
use Lucinda\Console\Terminal\ColorDepth;

final class Color
{
    private const NAMED = [
        "black" => [0, 0, 0, 30],
        "red" => [205, 49, 49, 31],
        "green" => [13, 188, 121, 32],
        "yellow" => [229, 229, 16, 33],
        "blue" => [36, 114, 200, 34],
        "magenta" => [188, 63, 188, 35],
        "cyan" => [17, 168, 205, 36],
        "white" => [229, 229, 229, 37],
        "bright-black" => [102, 102, 102, 90],
        "bright-red" => [241, 76, 76, 91],
        "bright-green" => [35, 209, 139, 92],
        "bright-yellow" => [245, 245, 67, 93],
        "bright-blue" => [59, 142, 234, 94],
        "bright-magenta" => [214, 112, 214, 95],
        "bright-cyan" => [41, 184, 219, 96],
        "bright-white" => [255, 255, 255, 97],
    ];
    private const ANSI_LEVELS = [0, 95, 135, 175, 215, 255];

    public function getAnsiCode(string $value, ColorDepth $depth, bool $background = false): ?string
    {
        if ($depth === ColorDepth::NONE) {
            return null;
        }
        $value = strtolower(trim($value));
        if ($value === "default") {
            return (string) ($background ? 49 : 39);
        }
        if ($depth === ColorDepth::ANSI256 && preg_match('/^ansi-(\d{1,3})$/', $value, $matches) && (int) $matches[1] <= 255) {
            return ($background ? "48" : "38").";5;".(int) $matches[1];
        }
        [$r, $g, $b, $ansi16] = $this->parse($value);
        if ($depth === ColorDepth::TRUE_COLOR) {
            return ($background ? "48" : "38").";2;".$r.";".$g.";".$b;
        }
        if ($depth === ColorDepth::ANSI256) {
            $index = $this->toAnsi256($r, $g, $b);
            return ($background ? "48" : "38").";5;".$index;
        }
        $code = $ansi16 ?? $this->nearestAnsi16($r, $g, $b);
        return (string) ($background ? ($code >= 90 ? $code+10 : $code+10) : $code);
    }

    public function validate(string $value): void
    {
        if (strtolower(trim($value)) === "default") {
            return;
        }
        $this->parse($value);
    }

    /** @return array{int,int,int,int|null} */
    private function parse(string $value): array
    {
        $value = strtolower(trim($value));
        if (isset(self::NAMED[$value])) {
            return self::NAMED[$value];
        }
        if (preg_match('/^#([0-9a-f]{6})$/', $value, $matches)) {
            return [
                hexdec(substr($matches[1], 0, 2)),
                hexdec(substr($matches[1], 2, 2)),
                hexdec(substr($matches[1], 4, 2)),
                null
                ];
        }
        if (preg_match('/^rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/', $value, $matches)) {
            $rgb = [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
            if (max($rgb) <= 255) {
                return [$rgb[0], $rgb[1], $rgb[2], null];
            }
        }
        if (preg_match('/^ansi-(\d{1,3})$/', $value, $matches) && (int) $matches[1] <= 255) {
            return $this->fromAnsi256((int) $matches[1]);
        }
        throw new Exception("Invalid color: ".$value);
    }

    private function toAnsi256(int $r, int $g, int $b): int
    {
        $levels = self::ANSI_LEVELS;
        $nearest = function (int $value) use ($levels): int {
            $best = 0;
            $distance = PHP_INT_MAX;
            foreach ($levels as $index => $level) {
                if (abs($value-$level) < $distance) {
                    $best = $index;
                    $distance = abs($value-$level);
                }
            }
            return $best;
        };
        return 16 + 36*$nearest($r) + 6*$nearest($g) + $nearest($b);
    }

    /** @return array{int,int,int,null} */
    private function fromAnsi256(int $index): array
    {
        if ($index < 16) {
            $named = array_values(self::NAMED)[$index];
            return [$named[0], $named[1], $named[2], null];
        }
        if ($index >= 232) {
            $value = 8+($index-232)*10;
            return [$value, $value, $value, null];
        }
        $levels = self::ANSI_LEVELS;
        $index -= 16;
        return [
            $levels[intdiv($index, 36)],
            $levels[intdiv($index%36, 6)],
            $levels[$index%6],
            null
            ];
    }

    private function nearestAnsi16(int $r, int $g, int $b): int
    {
        $best = 37;
        $distance = PHP_INT_MAX;
        foreach (self::NAMED as [$cr, $cg, $cb, $code]) {
            $candidate = ($r-$cr)**2 + ($g-$cg)**2 + ($b-$cb)**2;
            if ($candidate < $distance) {
                $distance = $candidate;
                $best = $code;
            }
        }
        return $best;
    }
}
