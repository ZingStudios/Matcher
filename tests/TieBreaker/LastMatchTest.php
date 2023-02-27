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
use Zing\Matcher\TieBreaker\LastMatch;

/**
 * @covers \Zing\Matcher\TieBreaker\LastMatch
 */
class LastMatchTest extends TestCase
{
    public function testLastMatchReturned()
    {
        $firstResult = new MatchResult('Test1', new LevensteinSimilarityResult(1));
        $lastResult = new MatchResult('Test2', new LevensteinSimilarityResult(1));

        $result = (new LastMatch())(
            'needle',
            [
                $firstResult,
                $lastResult,
            ]
        );

        $this->assertEquals($lastResult, $result);
        $this->assertSame($lastResult, $result);
    }

    /**
     * Verifies that a LengthException is throw if an empty array is passed
     * as the $tiedResults argument
     */
    public function testWillThrowOnEmptyResults()
    {
        $this->expectException(LengthException::class);
        $this->expectDeprecationMessage('Argument $tiedResults cannot be an empty array.');

        (new LastMatch())('needle', []);
    }
}
