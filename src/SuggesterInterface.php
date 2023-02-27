<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher;

interface SuggesterInterface
{
    /**
     * @param string $needle
     * @param array $haystack
     * @return MatchResultInterface[]
     */
    public function suggest(string $needle, array $haystack): array;
}
