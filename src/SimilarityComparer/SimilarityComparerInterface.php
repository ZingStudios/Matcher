<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer;

interface SimilarityComparerInterface
{
    public const MULTIBYTE = 1;

    /**
     * Compares the 2 string and returns a SimilarityResult
     * @param string $needle
     * @param string $hay
     * @return SimilarityResultInterface
     */
    public function compare(string $needle, string $hay): SimilarityResultInterface;

    /**
     * @param SimilarityResultInterface $result1
     * @param SimilarityResultInterface $result2
     * @return int
     * returns
     * 1 if $result1 is better than $result2
     * 0 if the 2 results are equal
     * -1 if $result2 is better than $result1
     */
    public function compareResults(SimilarityResultInterface $result1, SimilarityResultInterface $result2): int;
}