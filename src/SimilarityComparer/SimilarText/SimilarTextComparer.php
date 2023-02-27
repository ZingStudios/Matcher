<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer\SimilarText;

use Zing\Matcher\Exception\InvalidArgumentException;
use Zing\Matcher\SimilarityComparer\AbstractComparer;
use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

class SimilarTextComparer extends AbstractComparer
{
    public function compare(string $needle, string $hay): SimilarityResultInterface
    {
        $percent = 0;
        $similarity = $this->similarText($needle, $hay, $percent);
        return new SimilarTextSimilarityResult(
            $similarity,
            $percent
        );
    }

    /**
     * Calls the appropriate similar_text function based
     * on the MULTIBYTE flag.
     *
     * @param string $string1
     * @param string $string2
     * @param float $percent
     * @return int
     */
    private function similarText(
        string $string1,
        string $string2,
        float &$percent
    ): int {
        if ($this->isOptionEnabled(self::MULTIBYTE)) {
            return mb_similar_text(
                $string1,
                $string2,
                $percent
            );
        } else {
            return similar_text(
                $string1,
                $string2,
                $percent
            );
        }
    }

    public function compareResults(SimilarityResultInterface $result1, SimilarityResultInterface $result2): int
    {
        $this->assertSimilarityResultType($result1);
        $this->assertSimilarityResultType($result2);

        /**
         * @var SimilarTextSimilarityResult $result1
         * @var SimilarTextSimilarityResult $result2
         */
        return $result1->getPercent() <=> $result2->getPercent();
    }

    private function assertSimilarityResultType(SimilarityResultInterface $result)
    {
        if (!$result instanceof SimilarTextSimilarityResult) {
            throw new InvalidArgumentException(
                'Expected %s but received object of type %s',
                SimilarTextSimilarityResult::class,
                $result::class
            );
        }
    }
}
