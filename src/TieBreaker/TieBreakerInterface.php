<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\TieBreaker;

use Zing\Matcher\MatchResultInterface;

interface TieBreakerInterface
{
    /**
     * @param string $needle
     * @param MatchResultInterface[] $tiedResults
     * @return MatchResultInterface
     */
    public function __invoke(string $needle, array $tiedResults): MatchResultInterface;
}