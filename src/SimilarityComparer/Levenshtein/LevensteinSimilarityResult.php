<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer\Levenshtein;


use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

final class LevensteinSimilarityResult implements SimilarityResultInterface
{
    private int $distance;

    public function __construct(
        int $distance
    ) {
        $this->distance = $distance;
    }

    public function isExactMatch(): bool
    {
        return $this->getScore() === 0;
    }

    public function getScore(): int
    {
        return $this->distance;
    }
}
