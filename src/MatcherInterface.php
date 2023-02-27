<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher;

interface MatcherInterface
{
    public const ALLOW_EXACT_MATCH_TIES = 1;

    public function match(string $needle, array $haystack): MatchResultInterface;
}
