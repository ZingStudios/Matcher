<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\TieBreaker;

use Zing\Matcher\Exception\LengthException;
use Zing\Matcher\Exception\TieException;
use Zing\Matcher\MatchResultInterface;

/**
 * Doesn't really break a tie, instead it throws a TieException
 */
final class ThrowException implements TieBreakerInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $needle, array $tiedResults): MatchResultInterface
    {
        if (count($tiedResults) === 0) {
            throw new LengthException('Argument $tiedResults cannot be an empty array.');
        }

        throw new TieException($tiedResults);
    }
}
