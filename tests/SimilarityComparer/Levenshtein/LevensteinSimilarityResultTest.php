<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\SimilarityComparer\Levenshtein;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinSimilarityResult;

/**
 * @covers \Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinSimilarityResult
 */
class LevensteinSimilarityResultTest extends TestCase
{
    /**
     * Verifies that construction of the result value object
     * and it's getters work as expected.
     */
    public function testValueObject()
    {
        $result = new LevensteinSimilarityResult(
            13
        );

        $this->assertEquals(13, $result->getScore());
    }
}
