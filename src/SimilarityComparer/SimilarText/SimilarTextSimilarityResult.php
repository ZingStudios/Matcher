<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer\SimilarText;


use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

final class SimilarTextSimilarityResult implements SimilarityResultInterface
{
    private int $similarity;
    private float $percent;

    public function __construct(
        int $similarity,
        float $percent
    ) {
        $this->similarity = $similarity;
        $this->percent = $percent;
    }

    public function getScore(): int
    {
        return $this->similarity;
    }

    public function isExactMatch(): bool
    {
        return $this->getPercent() == 100;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }
}
