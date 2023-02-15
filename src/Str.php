<?php

/**
 * Copyright (C) 2023 Greg Roach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest;

use Stringable;

use function htmlspecialchars;
use function mb_strlen;
use function mb_substr;
use function preg_match;
use function str_ends_with;
use function str_starts_with;
use function strtr;

/**
 * Fluent, immutable, UTF8 string handling.
 */
final class Str implements Stringable
{
    private const UTF8_WHITESPACE = [
        " ",
        "\0",
        "\n",
        "\r",
        "\t",
        "\v",
        "\u{85}", // NEL - newline
        "\u{2028}", // LS - line separator
        "\u{2029}", // PS - paragraph separator
    ];

    /**
     * @param string $str
     */
    public function __construct(private readonly string $str)
    {
    }

    /**
     * Fluent constructor.
     *
     * @param Stringable|string $str
     *
     * @return $this
     */
    public static function make(Stringable|string $str): self
    {
        return new self((string) $str);
    }

    /**
     * @return $this
     */
    public function e(): self
    {
        return new Str(htmlspecialchars(string: $this->str, encoding: 'UTF-8'));
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return mb_strlen($this->str);
    }

    /**
     * @param string                        $regex
     * @param array<int|string,string>|null $match
     *
     * @return bool
     */
    public function match(string $regex, array &$match = null): bool
    {
        return preg_match($regex, $this->str, $match) === 1;
    }

    /**
     * @param int      $offset
     * @param int|null $length
     *
     * @return $this
     */
    public function substr(int $offset, int $length = null): self
    {
        return new Str(mb_substr($this->str, $offset, $length));
    }

    /**
     * @param array<int|string,string> $replace_pairs
     *
     * @return $this
     */
    public function tr(array $replace_pairs): self
    {
        return new Str(strtr($this->str, $replace_pairs));
    }

    /**
     * PHP::trim() works on bytes, not characters
     *
     * @param array<string> $characters
     *
     * @return $this
     */
    public function trim(array $characters = self::UTF8_WHITESPACE): self
    {
        $str = $this->str;

        do {
            $done = true;

            foreach ($characters as $character) {
                while (str_starts_with($str, $character)) {
                    $str  = mb_substr($str, mb_strlen($character));
                    $done = false;
                }
                while (str_ends_with($str, $character)) {
                    $str  = mb_substr($str, 0, -mb_strlen($character));
                    $done = false;
                }
            }
        } while (!$done);

        return new self($str);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->str;
    }
}
