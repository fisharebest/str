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

namespace Fisharebest\Test;

use Fisharebest\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function htmlspecialchars;
use function mb_substr;
use function preg_match;
use function trim;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Str::class)]
final class StrTest extends TestCase
{
    #[DataProvider('data')]
    public function testConstructor(string $data): void
    {
        self::assertSame(
            expected: $data,
            actual: (string) new Str(str: $data),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testStaticConstructor(string $data): void
    {
        self::assertSame(
            expected: $data,
            actual: (string) Str::make(str: $data),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testToString(string $data): void
    {
        self::assertSame(
            expected: $data,
            actual: Str::make(str: $data)->__toString(),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testHtmlSpecialChars(string $data): void
    {
        self::assertSame(
            expected: htmlspecialchars($data),
            actual: (string) Str::make(str: $data)->e(),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testLength(string $data): void
    {
        self::assertSame(
            expected: mb_strlen($data),
            actual: Str::make(str: $data)->length(),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testMatch(string $data): void
    {
        self::assertSame(
            expected: (bool) preg_match('/[a-f]/', $data),
            actual: Str::make(str: $data)->match('/[a-f]/'),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testSubstr(string $data): void
    {
        foreach ([0, 1, -1, 2, -2] as $offset) {
            self::assertSame(
                expected: mb_substr(string: $data, start: $offset),
                actual: (string) Str::make(str: $data)->substr(offset: $offset),
                message: $data
            );

            foreach ([0, 1, -1, 2, -2] as $length) {
                self::assertSame(
                    expected: mb_substr(string: $data, start: $offset, length: $length),
                    actual: (string) Str::make(str: $data)->substr(offset: $offset, length: $length),
                    message: $data
                );
            }
        }
    }

    #[DataProvider('data')]
    public function testTr(string $data): void
    {
        self::assertSame(
            expected: strtr($data, ['o' => 'O']),
            actual: (string) Str::make(str: $data)->tr(replace_pairs: ['o' => 'O']),
            message: $data
        );
    }

    #[DataProvider('data')]
    public function testTrim(string $data): void
    {
        self::assertSame(
            expected: trim(string: $data),
            actual: (string) Str::make(str: $data)->trim(),
            message: $data
        );

        self::assertSame(
            expected: trim(string: $data, characters: '<>'),
            actual: (string) Str::make(str: $data)->trim(characters: ['<', '>']),
            message: $data
        );
    }

    /**
     * Pass every test string to every function.
     *
     * @return array<int,array<int,string>>
     */
    public static function data(): array
    {
        return [
            [''],
            ["C with cedilla: \u{C7}"],
            ['Foo'],
            ['foo'],
            ['<script>evil()</script>'],
        ];
    }
}
