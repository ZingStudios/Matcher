<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\Exception\TieException;
use Zing\Matcher\Result;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinSimilarityResult;

/**
 * @covers \Zing\Matcher\Exception\TieException
 */
class TieExceptionTest extends TestCase
{
    /**
     * Verifies that the results that are passed when making the exception
     * are the same as those return by getTiedResults.
     */
    public function testGetTieResults()
    {
        $tiedResults = [
            new LevensteinSimilarityResult( 1),
            new LevensteinSimilarityResult( 1),
        ];

        $exception = new TieException($tiedResults);

        $this->assertEquals($tiedResults, $exception->getTiedResults());
    }
}
