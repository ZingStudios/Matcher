<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\TieBreaker;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\Exception\LengthException;
use Zing\Matcher\MatchResult;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinSimilarityResult;
use Zing\Matcher\TieBreaker\FirstMatch;

/**
 * @covers \Zing\Matcher\TieBreaker\FirstMatch
 */
class FirstMatchTest extends TestCase
{
    /**
     * Verifies that the first element in the tied results array is returned
     */
    public function testFirstMatchReturned()
    {
        $firstResult = new MatchResult('Test1', new LevensteinSimilarityResult(1));
        $lastResult = new MatchResult('Test2', new LevensteinSimilarityResult(1));

        $result = (new FirstMatch())(
            'needle',
            [
                $firstResult,
                $lastResult,
            ]
        );

        $this->assertEquals($firstResult, $result);
        $this->assertSame($firstResult, $result);
    }

    /**
     * Verifies that a LengthException is throw if an empty array is passed
     * as the $tiedResults argument
     */
    public function testWillThrowOnEmptyResults()
    {
        $this->expectException(LengthException::class);
        $this->expectDeprecationMessage('Argument $tiedResults cannot be an empty array.');

        (new FirstMatch())('needle', []);
    }
}
